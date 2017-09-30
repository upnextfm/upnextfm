<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use AppBundle\Entity\Vote;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Room;
use AppBundle\Entity\VideoLog;
use AppBundle\Entity\VideoRepository;
use AppBundle\EventListener\Event\PlayedVideoEvent;
use AppBundle\EventListener\Event\UserEvents;
use AppBundle\Playlist\ProvidersInterface;
use AppBundle\Playlist\RngMod;
use AppBundle\Service\VideoService;
use AppBundle\Storage\PlaylistStorage;

class VideoListener extends AbstractListener
{
  /**
   * @var PlaylistStorage
   */
  protected $playlist;

  /**
   * @var RngMod
   */
  protected $rngmod;

  /**
   * @var ProvidersInterface
   */
  protected $providers;

  /**
   * @var VideoService
   */
  protected $videoService;

  /**
   * @var VideoRepository
   */
  protected $videoRepo;

  /**
   * @param PlaylistStorage $playlist
   * @return $this
   */
  public function setPlaylistStorage(PlaylistStorage $playlist)
  {
    $this->playlist = $playlist;
    return $this;
  }

  /**
   * @param RngMod $rngMod
   * @return $this
   */
  public function setRngMod(RngMod $rngMod)
  {
    $this->rngmod = $rngMod;
    return $this;
  }

  /**
   * @param ProvidersInterface $providers
   * @return $this
   */
  public function setProviders(ProvidersInterface $providers)
  {
    $this->providers = $providers;
    return $this;
  }

  /**
   * @param VideoService $videoService
   * @return $this
   */
  public function setVideoService(VideoService $videoService)
  {
    $this->videoService = $videoService;
    return $this;
  }

  /**
   * @param VideoRepository $videoRepo
   * @return $this
   */
  public function setVideoRepository(VideoRepository $videoRepo)
  {
    $this->videoRepo = $videoRepo;
    return $this;
  }

  /**
   * @param UserInterface $user
   * @param Room $room
   * @param string $url
   * @return VideoListener|bool
   */
  public function onAppend(UserInterface $user, Room $room, $url)
  {
    $parsed = $this->providers->parseURL($url);
    if (!$parsed) {
      // return $this->dispatchError("Invalid URL \"${$url}\".");
    }

    if ($parsed["playlist"]) {
      $codenames = $this->videoService->getPlaylist($parsed["codename"], $parsed["provider"]);
      foreach ($codenames as $codename) {
        $video = $this->getOrCreateVideo(
          $room,
          $user,
          $codename,
          $parsed["provider"]
        );
        if ($video) {
          /** @var VideoLog $videoLog */
          $video->setDateLastPlayed(new \DateTime());
          $video->incrNumPlays();
          $videoLog = new VideoLog($video, $room, $user);
          $videoLog = $this->em->merge($videoLog);
          $this->em->flush();

          $this->playlist->append($videoLog);
          $event = new PlayedVideoEvent($user, $room, $video);
          $this->eventDispatcher->dispatch(UserEvents::PLAYED_VIDEO, $event);
        }
      }
    } else {
      $video = $this->getOrCreateVideo(
        $room,
        $user,
        $parsed["codename"],
        $parsed["provider"]
      );
      if (!$video) {
        return true;
      }

      /** @var VideoLog $videoLog */
      $video->setDateLastPlayed(new \DateTime());
      $video->incrNumPlays();
      $videoLog = new VideoLog($video, $room, $user);
      $videoLog = $this->em->merge($videoLog);
      $this->em->flush();

      $this->playlist->append($videoLog);
      $event = new PlayedVideoEvent($user, $room, $video);
      $this->eventDispatcher->dispatch(UserEvents::PLAYED_VIDEO, $event);
    }

    usleep(500);

    return $this->sendPlaylistToRoom($room);
  }

  /**
   * @param UserInterface $user
   * @param Room $room
   * @param int $videoID
   * @return VideoListener|void
   */
  public function onRemove(UserInterface $user, Room $room, $videoID)
  {
    if (empty($videoID)) {
      return $this->logger->error("Invalid videoID.");
    }

    $result = $this->playlist->removeByID($room, $videoID);
    if (is_array($result)) {
      $videoLog = $result["videoLog"];
      $event    = new PlaylistResponseEvent($room, "playlist:playlistVideos", [
        0,
        $this->serializeVideo($videoLog)
      ]);
      $this->eventDispatcher->dispatch(SocketEvents::PLAYLIST_RESPONSE, $event);
    }

    return $this->sendPlaylistToRoom($room);
  }

  /**
   * @param UserInterface $user
   * @param Room $room
   * @param $videoID
   * @return VideoListener|void
   */
  public function onPlayNext(UserInterface $user, Room $room, $videoID)
  {
    if (empty($videoID)) {
      return $this->logger->error("Invalid videoID.");
    }

    $this->playlist->playNext($room, $videoID);
    usleep(500);

    return $this->sendPlaylistToRoom($room);
  }

  /**
   * @param UserInterface|User $user
   * @param Room $room
   * @param int $videoID
   * @param int $value
   */
  public function onVote(UserInterface $user, Room $room, $videoID, $value)
  {
    if (empty($videoID)) {
      return $this->logger->error("Invalid videoID.");
    }
    if ($value != 1 && $value != -1) {
      return $this->logger->error("Invalid vote value ${value}.");
    }

    $video = $this->em->getRepository("AppBundle:VideoLog")
      ->findByID($videoID)->getVideo();
    $hasVoted = $this->em->getRepository("AppBundle:Vote")
      ->hasVoted($user, $video);

    if (!$hasVoted) {
      $vote = new Vote();
      $vote->setValue($value);
      $vote->setVideo($video);
      $vote->setUser($user);

      $this->em->persist($vote);
      $this->em->flush();
    } else {
      var_dump("You have already voted on this video!");
    }
  }

  /**
   * @param Room $room
   * @param bool $playOnEmpty
   * @return $this
   */
  private function sendPlaylistToRoom(Room $room, $playOnEmpty = true)
  {
    if ($playOnEmpty) {
      if (!$this->playlist->getCurrent($room)) {
        if ($current = $this->playlist->popToCurrent($room)) {
          $videoLog = $current["videoLog"];
          $event = new PlaylistResponseEvent($room, "playlist:playlistStart", [
            0,
            $this->serializeVideo($videoLog)
          ]);
          $this->eventDispatcher->dispatch(SocketEvents::PLAYLIST_RESPONSE, $event);
        }
      }
    }

    $videos = [];
    foreach ($this->playlist->getAll($room) as $videoLog) {
      $videos[] = $this->serializeVideo($videoLog);
    }
    $event = new PlaylistResponseEvent($room, "playlist:playlistVideos", [
      $videos
    ]);
    $this->eventDispatcher->dispatch(SocketEvents::PLAYLIST_RESPONSE, $event);
  }

  /**
   * @param VideoLog $videoLog
   * @return array
   */
  protected function serializeVideo(VideoLog $videoLog)
  {
    $video = $videoLog->getVideo();

    return [
      "id"        => $videoLog->getId(),
      "codename"  => $video->getCodename(),
      "provider"  => $video->getProvider(),
      "permalink" => $video->getPermalink(),
      "thumbnail" => $video->getThumbSm(),
      "title"     => $video->getTitle(),
      "seconds"   => $video->getSeconds(),
      "playedBy"  => $videoLog->getUser()->getUsername(),
      "createdBy" => $video->getCreatedByUser()->getUsername()
    ];
  }

  /**
   * @param Room $room
   * @param UserInterface|User $user
   * @param string $codename
   * @param string $provider
   * @return Video|null
   */
  private function getOrCreateVideo(Room $room, UserInterface $user, $codename, $provider)
  {
    $video = $this->videoRepo->findByCodename($codename, $provider);
    if (!$video) {
      $info = $this->videoService->getInfo($codename, $provider);
      if (!$info) {
        return null;
      }

      $video = new Video();
      $video->setCodename($info->getCodename());
      $video->setProvider($info->getProvider());
      $video->setCreatedByUser($user);
      $video->setCreatedInRoom($room);
      $video->setTitle($info->getTitle());
      $video->setSeconds($info->getSeconds());
      $video->setPermalink($info->getPermalink());
      $video->setThumbColor($info->getThumbColor());
      $video->setThumbSm($info->getThumbnail("sm"));
      $video->setThumbMd($info->getThumbnail("md"));
      $video->setThumbLg($info->getThumbnail("lg"));
      $video->setNumPlays(0);
      $this->em->persist($video);
    }

    return $video;
  }
}
