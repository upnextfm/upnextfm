<?php
namespace AppBundle\Topic;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoLog;
use AppBundle\Entity\VideoRepository;
use AppBundle\Entity\Vote;
use AppBundle\EventListener\Event\PlayedVideoEvent;
use AppBundle\EventListener\Event\UserEvents;
use AppBundle\EventListener\Socket\PlaylistResponseEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\VideoRequestEvent;
use AppBundle\Playlist\ProvidersInterface;
use AppBundle\Playlist\RngMod;
use AppBundle\Service\VideoService;
use AppBundle\Storage\PlaylistStorage;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerTrait;
use Predis\Client as Redis;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoTopic extends AbstractTopic implements TopicPeriodicTimerInterface, EventSubscriberInterface
{
  use TopicPeriodicTimerTrait;

  /**
   * @var array
   */
  protected $subs = [];

  /**
   * @var Topic[]
   */
  protected $rooms = [];

  /**
   * @var PlaylistStorage
   */
  protected $playlist;

  /**
   * @var RngMod
   */
  protected $rngmod;

  /**
   * @var Redis
   */
  protected $redis;

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
   * {@inheritdoc}
   */
  public function getName()
  {
    return "video.topic";
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      SocketEvents::PLAYLIST_RESPONSE => "onPlaylistResponse"
    ];
  }

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
   * @param Redis $redis
   * @return $this
   */
  public function setRedis(Redis $redis)
  {
    $this->redis = $redis;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      $user = null;
    }
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    if (!$room) {
      $this->logger->error("Room not found or created.");
      return;
    }

    $client = ["conn" => $conn, "id" => $topic->getId()];
    $roomName = $room->getName();
    if (!isset($this->subs[$roomName])) {
      $this->subs[$roomName] = [];
    }
    if (!empty($this->subs[$roomName])) {
      $index = array_search($client, $this->subs[$roomName]);
      if (false !== $index) {
        unset($this->subs[$roomName][$index]);
      }
    }
    $this->subs[$roomName][] = $client;
    $this->rooms[$roomName]  = $topic;

    $videos = [];
    foreach ($this->playlist->getAll($room) as $videoLog) {
      $videos[] = $this->serializeVideo($videoLog);
    }

    if ($videos) {
      $this->dispatchToUser(
        "playlist:playlistVideos",
        $videos
      );
    }
    $current = $this->playlist->getCurrent($room);
    if ($current) {
      /** @var VideoLog $videoLog */
      if ($videoLog = $current["videoLog"]) {
        $this->dispatchToUser(
          "playlist:playlistStart",
          time() - $current["timeStarted"],
          $this->serializeVideo($videoLog)
        );
      }
    }

    $this->flush($conn, $topic);
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      $user = null;
    }
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    if (!$room) {
      $this->logger->error("Room not found or created.");
      return;
    }

    $roomName = $room->getName();
    if (!isset($this->subs[$roomName])) {
      $this->subs[$roomName] = [];
    }
    if (!empty($this->subs[$roomName])) {
      $client = ["conn" => $conn, "id" => $topic->getId()];
      $index = array_search($client, $this->subs[$roomName]);
      if (false !== $index) {
        unset($this->subs[$roomName][$index]);
        if (count($this->subs[$roomName]) === 0) {
          unset($this->subs[$roomName]);
          unset($this->rooms[$roomName]);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onPublish(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    $event,
    array $exclude,
    array $eligible
  )
  {
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      return $this->logger->error("User not found.", $event);
    }
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->getIsDeleted()) {
      return $this->logger->error("Room not found.", $event);
    }

    // @see AppBundle\EventListener\Socket\SocketSubscriber
    return $this->eventDispatcher->dispatch(
      SocketEvents::VIDEO_REQUEST,
      new VideoRequestEvent($room, $user, $event)
    );
  }

  /**
   * @param PlaylistResponseEvent $event
   */
  public function onPlaylistResponse(PlaylistResponseEvent $event)
  {
    $topic = $this->rooms[$event->getRoom()->getName()];
    if ($topic) {
      $topic->broadcast([
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]);
    }
  }

  /**
   * @param Topic $topic
   *
   * @return mixed
   */
  public function registerPeriodicTimer(Topic $topic)
  {
    $this->periodicTimer->addPeriodicTimer(
      $this,
      VideoCommands::TIME_UPDATE,
      $this->container->getParameter("app_ws_video_time_update_interval"),
      function () use ($topic) {

        /** @var VideoLog $videoLog */
        foreach ($this->rooms as $roomName => $topic) {
          $room = $this->em->getRepository("AppBundle:Room")->findByName($roomName);
          $current = $this->playlist->getCurrent($room);
          if (!$current) {
            $current = $this->playlist->popToCurrent($room);
          }

          if ($current) {
            $videoLog      = $current["videoLog"];
            $videoSecs     = $videoLog->getVideo()->getSeconds();
            $timeFinishes  = $current["timeStarted"] + $videoSecs;
            $timeRemaining = $timeFinishes - time();

            if ($timeRemaining <= 0) {
              if ($current = $this->playlist->popToCurrent($room)) {
                $videoLog = $current["videoLog"];
                $this->dispatchToRoom(
                  "playlist:playlistStart",
                  0,
                  $this->serializeVideo($videoLog)
                );
                $this->sendPlaylistToRoom($room);
              } else {
                $this->playlist->clearCurrent($room);
                $this->dispatchToRoom("playlist:playlistStop");
                $this->sendPlaylistToRoom($room);
              }
            } else {
              $this->dispatchToRoom(
                "player:playerTime",
                $videoSecs - $timeRemaining
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

          $this->flush($this->subs[$roomName], $topic);
        }
      }
    );

    $this->periodicTimer->addPeriodicTimer(
      $this,
      "append",
      1,
      function () use ($topic) {
        $item = $this->redis->lpop("playlist:append");
        if ($item) {
          $item  = json_decode($item, true);
          $user  = $this->em->getRepository("AppBundle:User")->findByUsername($item["username"]);
          $room  = $this->em->getRepository("AppBundle:Room")->findByName($item["roomName"]);
          $video = $this->em->getRepository("AppBundle:Video")->findByID($item["videoID"]);

          /** @var VideoLog $videoLog */
          $video->setDateLastPlayed(new \DateTime());
          $video->incrNumPlays();
          $videoLog = new VideoLog($video, $room, $user);
          $videoLog = $this->em->merge($videoLog);
          $this->em->flush();

          $this->playlist->append($videoLog);
          $event = new PlayedVideoEvent($user, $room, $videoLog->getVideo());
          $this->eventDispatcher->dispatch(UserEvents::PLAYED_VIDEO, $event);
          usleep(500);

          $this->sendPlaylistToRoom($room);
        }
      }
    );
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
          $this->dispatchToRoom(
            "playlist:playlistStart",
            0,
            $this->serializeVideo($videoLog)
          );
        }
      }
    }

    $videos = [];
    foreach ($this->playlist->getAll($room) as $videoLog) {
      $videos[] = $this->serializeVideo($videoLog);
    }
    $this->dispatchToRoom(
      "playlist:playlistVideos",
      $videos
    );

    return $this->flush($this->subs[$room->getName()]);
  }
}
