<?php
namespace AppBundle\Topic;

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

/**
 * Dispatches video/playlist related frontend commands to backend event listeners.
 *
 * @see AppBundle\EventListener\Socket\VideoListener
 */
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
   * Listens for responses which should be sent to the room playlist
   *
   * @outgoing
   * @param PlaylistResponseEvent $event
   * @return Topic|void
   */
  public function onPlaylistResponse(PlaylistResponseEvent $event)
  {
    $roomName = $event->getRoom()->getName();
    if (!isset($this->rooms[$roomName])) {
      return $this->logger->error(sprintf(
        'Room "%s" does not exist.',
        $roomName
      ));
    }

    return $this->rooms[$roomName]->broadcast([
      "dispatch" => [
        ["action" => $event->getAction(), "args" => $event->getArgs()]
      ]
    ]);
  }

  /**
   * Listens for responses which should be sent to a specific user
   *
   * @outgoing
   * @param UserResponseEvent $event
   * @return \Ratchet\Wamp\WampConnection|void
   */
  public function onUserPlaylistResponse(UserResponseEvent $event)
  {
    $username = $event->getUser()->getUsername();
    if (!isset($this->subs[$username])) {
      return $this->logger->error(sprintf(
        'User "%s" not subscribed to videos.',
        $username
      ));
    }

    $subscriber = $this->subs[$username];
    return $subscriber->getConnection()->event(
      $subscriber->getTopic()->getId(),
      [
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]
    );
  }

  /**
   * Called when a user joins the room
   *
   * {@inheritdoc}
   *
   * @incoming
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    // Save the connection and topic for the user and room, so we can access
    // them later by username/name.
    $user = $this->getUser($conn);
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    $this->rooms[$room->getName()]    = $topic;
    $this->subs[$user->getUsername()] = new Subscriber($conn, $topic);

    // Dispatch a request to the video listeners to have the room playlist
    // sent to the connected client.
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
   * Called when a user leaves the room
   *
   * {@inheritdoc}
   *
   * @incoming
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    // Remove the user from the list of subscribers.
    $user = $this->getUser($conn);
    unset($this->subs[$user->getUsername()]);
  }

  /**
   * Called when a user sends a command to the room
   *
   * {@inheritdoc}
   *
   * @incoming
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
   * @param Topic $topic
   * @return mixed
   */
  public function registerPeriodicTimer(Topic $topic)
  {
    $this->periodicTimer->addPeriodicTimer($this, "timeUpdate", 5, function () {
      foreach ($this->rooms as $roomName => $topic) {
        $room = $this->roomRepository->findByName($roomName);
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
