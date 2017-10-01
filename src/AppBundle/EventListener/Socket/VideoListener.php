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

/**
 * Handles client side events related to the video topic.
 */
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
   * @param Room $room
   * @param UserInterface $user
   * @param string $url
   * @return VideoListener|bool
   */
  public function onAppend(Room $room, UserInterface $user, $url)
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
   * @param Room $room
   * @param UserInterface $user
   * @param int $videoID
   * @return VideoListener|void
   */
  public function onRemove(Room $room, UserInterface $user, $videoID)
  {
    if (empty($videoID)) {
      return $this->logger->error("Invalid videoID.");
    }

    $result = $this->playlist->removeByID($room, $videoID);
    if (is_array($result)) {
      $videoLog = $result["videoLog"];
      $event    = new PlaylistResponseEvent($room, PlaylistActions::VIDEOS, [
        0,
        $this->serializeVideo($videoLog)
      ]);
      $this->eventDispatcher->dispatch(SocketEvents::PLAYLIST_RESPONSE, $event);
    }

    return $this->sendPlaylistToRoom($room);
  }

  /**
   * @param Room $room
   * @param UserInterface $user
   * @param $videoID
   * @return VideoListener|void
   */
  public function onPlayNext(Room $room, UserInterface $user, $videoID)
  {
    if (empty($videoID)) {
      return $this->logger->error("Invalid videoID.");
    }

    $this->playlist->playNext($room, $videoID);
    usleep(500);

    return $this->sendPlaylistToRoom($room);
  }

  /**
   * @param Room $room
   * @param UserInterface|User $user
   * @param int $videoID
   * @param int $value
   */
  public function onVote(Room $room, UserInterface $user, $videoID, $value)
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
   * @param UserInterface|null $user
   */
  public function onSendPlaylistToRoom(Room $room, UserInterface $user = null)
  {
    $this->sendPlaylistToRoom($room);
  }

  /**
   * @param Room $room
   * @param UserInterface $user
   */
  public function onSendPlaylistToUser(Room $room, UserInterface $user)
  {
    $videos = [];
    foreach ($this->playlist->getAll($room) as $videoLog) {
      $videos[] = $this->serializeVideo($videoLog);
    }
    if ($videos) {
      $this->eventDispatcher->dispatch(
        SocketEvents::USER_PLAYLIST_RESPONSE,
        new UserResponseEvent($user, PlaylistActions::VIDEOS, [
          $videos
        ])
      );
    }
  }

  /**
   * @param Room $room
   * @param UserInterface $user
   */
  public function onSendCurrentToUser(Room $room, UserInterface $user)
  {
    $current = $this->playlist->getCurrent($room);
    if ($current) {
      /** @var VideoLog $videoLog */
      if ($videoLog = $current["videoLog"]) {
        $this->eventDispatcher->dispatch(
          SocketEvents::USER_PLAYLIST_RESPONSE,
          new UserResponseEvent($user, PlaylistActions::START, [
            time() - $current["timeStarted"],
            $this->serializeVideo($videoLog)
          ])
        );
      }
    }
  }

  /**
   * @param Room $room
   * @param UserInterface|null $user
   */
  public function onPeriodicPlaylistUpdate(Room $room, UserInterface $user = null)
  {
    $current = $this->playlist->getCurrent($room);
    if (!$current) {
      $current = $this->playlist->popToCurrent($room);
    }

    if ($current) {
      /** @var VideoLog $videoLog */
      $videoLog      = $current["videoLog"];
      $videoSecs     = $videoLog->getVideo()->getSeconds();
      $timeFinishes  = $current["timeStarted"] + $videoSecs;
      $timeRemaining = $timeFinishes - time();

      if ($timeRemaining <= 0) {
        if ($current = $this->playlist->popToCurrent($room)) {
          $videoLog = $current["videoLog"];
          $this->eventDispatcher->dispatch(
            SocketEvents::PLAYLIST_RESPONSE,
            new PlaylistResponseEvent($room, PlaylistActions::START, [
              0,
              $this->serializeVideo($videoLog)
            ])
          );
          $this->sendPlaylistToRoom($room);
        } else {
          $this->playlist->clearCurrent($room);
          $this->eventDispatcher->dispatch(
            SocketEvents::PLAYLIST_RESPONSE,
            new PlaylistResponseEvent($room, PlaylistActions::STOP, [])
          );
          $this->sendPlaylistToRoom($room);
        }
      } else {
        $this->eventDispatcher->dispatch(
          SocketEvents::PLAYLIST_RESPONSE,
          new PlaylistResponseEvent($room, PlaylistActions::TIME, [
            $videoSecs - $timeRemaining
          ])
        );
      }
    } else {
      if ($logs = $this->rngmod->findByRoom($room, 3)) {
        foreach ($logs as $videoLog) {
          $this->playlist->append($videoLog);
          $event = new PlayedVideoEvent($videoLog->getUser(), $room, $videoLog->getVideo());
          $this->eventDispatcher->dispatch(UserEvents::PLAYED_VIDEO, $event);
        }
      }

      $this->sendPlaylistToRoom($room);
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
          $event = new PlaylistResponseEvent($room, PlaylistActions::START, [
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
    $event = new PlaylistResponseEvent($room, PlaylistActions::VIDEOS, [
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
