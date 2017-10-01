<?php
namespace AppBundle\Topic;

use AppBundle\Entity\Room;
use AppBundle\EventListener\Socket\PlaylistResponseEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\UserResponseEvent;
use AppBundle\EventListener\Socket\VideoRequestEvent;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicPeriodicTimerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class VideoTopic extends AbstractTopic implements TopicPeriodicTimerInterface, EventSubscriberInterface
{
  use TopicPeriodicTimerTrait;

  /**
   * @var Subscriber[]
   */
  protected $subs = [];

  /**
   * @var Topic[]
   */
  protected $rooms = [];

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
      SocketEvents::PLAYLIST_RESPONSE      => "onPlaylistResponse",
      SocketEvents::USER_PLAYLIST_RESPONSE => "onUserPlaylistResponse"
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user     = $this->getUser($conn);
    $username = $user->getUsername();
    $room     = $this->getRoom($request->getAttributes()->get("room"), $user);
    $roomName = $room->getName();
    if (!$room) {
      $this->logger->error("Room not found or created.");
      return;
    }

    $this->subs[$username]  = new Subscriber($conn, $topic);
    $this->rooms[$roomName] = $topic;

    $this->eventDispatcher->dispatch(
      SocketEvents::VIDEO_REQUEST,
      new VideoRequestEvent($room, $user, [
        "dispatch" => [
          ["action" => "sendPlaylistToUser", "args" => []]
        ]
      ])
    );
    $this->eventDispatcher->dispatch(
      SocketEvents::VIDEO_REQUEST,
      new VideoRequestEvent($room, $user, [
        "dispatch" => [
          ["action" => "sendCurrentToUser", "args" => []]
        ]
      ])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($conn);
    unset($this->subs[$user->getUsername()]);
  }

  /**
   * {@inheritdoc}
   */
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $payload, array $ex, array $el)
  {
    $user = $this->getUser($conn);
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->getIsDeleted()) {
      return $this->logger->error("Room not found.", $payload);
    }

    // @see AppBundle\EventListener\Socket\SocketSubscriber
    return $this->eventDispatcher->dispatch(
      SocketEvents::VIDEO_REQUEST,
      new VideoRequestEvent($room, $user, $payload)
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
   * @param UserResponseEvent $event
   */
  public function onUserPlaylistResponse(UserResponseEvent $event)
  {
    $username = $event->getUser()->getUsername();
    if ($subscriber = $this->subs[$username]) {
      $conn  = $subscriber->getConnection();
      $topic = $subscriber->getTopic();
      $conn->event($topic->getId(), [
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
    $roomRepo = $this->em->getRepository("AppBundle:Room");
    $interval = $this->container->getParameter("app_ws_video_time_update_interval");

    $this->periodicTimer->addPeriodicTimer($this, "timeUpdate", $interval, function () use($roomRepo) {
      foreach ($this->rooms as $roomName => $topic) {
        $room = $roomRepo->findByName($roomName);
        $this->eventDispatcher->dispatch(
          SocketEvents::VIDEO_REQUEST,
          new VideoRequestEvent($room, null, [
            "dispatch" => [
              ["action" => "periodicPlaylistUpdate", "args" => []]
            ]
          ])
        );
      }
    });

/*    $this->periodicTimer->addPeriodicTimer(
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
    );*/
  }
}
