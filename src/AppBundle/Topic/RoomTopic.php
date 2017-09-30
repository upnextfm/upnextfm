<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomSettings;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSettings;
use AppBundle\EventListener\Socket\RoomRequestEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\RoomResponseEvent;
use AppBundle\EventListener\Socket\UserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Exception;
use Ratchet\Wamp\WampConnection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RoomTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var int
   */
  protected $noticeID = 0;

  /**
   * @var array
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
    return "room.topic";
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      SocketEvents::ROOM_RESPONSE => "onRoomResponse",
      SocketEvents::USER_RESPONSE => "onUserResponse"
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    /** @var User $user */
    $user     = $this->getUser($conn);
    $username = null;
    if (!($user instanceof UserInterface)) {
      $username = $user;
      $user     = new User($username);
    } else {
      $username = $user->getUsername();
    }

    $room     = $this->getRoom($request->getAttributes()->get("room"), $user);
    $roomName = $room->getName();
    if ($user) {
      $this->roomStorage->addUser($room, $user);
    }

    $this->subs[$username]  = ["conn" => $conn, "topic" => $topic];
    $this->rooms[$roomName] = $topic;

    $repo = $this->em->getRepository("AppBundle:ChatLog");
    $messages = $repo->findRecent($room, $this->getParameter("app_room_recent_messages_count"));
    $repoFound = [];
    $repoUsers = [];
    foreach ($messages as $message) {
      $u = $message->getUser();
      if ($u && !in_array($u->getUsername(), $repoFound)) {
        $repoUsers[] = $this->serializeUser($message->getUser());
        $repoFound[] = $u->getUsername();
      }
    }

    $users = [];
    foreach ($topic as $client) {
      $u = $this->getUser($client);
      if ($u instanceof UserInterface) {
        $users[] = $u->getUsername();
        if (!in_array($u->getUsername(), $repoFound)) {
          $repoUsers[] = $this->serializeUser($u);
          $repoFound[] = $u->getUsername();
        }
      }
    }

    $settings = new UserSettings();
    if ($user) {
      $settings = $user->getSettings();
      if (!$settings) {
        $settings = new UserSettings();
      }
    }

    $this->eventDispatcher->dispatch(
      SocketEvents::USER_RESPONSE,
      new UserResponseEvent($user, "settings:settingsAll", [
        [
          "site" => $this->container->getParameter("app_site_settings"),
          "user" => $this->serializeUserSettings($settings),
          "room" => $this->serializeRoomSettings($room->getSettings())
        ]
      ])
    );
    $this->eventDispatcher->dispatch(
      SocketEvents::USER_RESPONSE,
      new UserResponseEvent($user, "room:roomMessages", [
        array_reverse($this->serializeMessages($messages))
      ])
    );
    $this->eventDispatcher->dispatch(
      SocketEvents::USER_RESPONSE,
      new UserResponseEvent($user, "users:usersRepoAddMulti", [
        $repoUsers
      ])
    );
    $this->eventDispatcher->dispatch(
      SocketEvents::USER_RESPONSE,
      new UserResponseEvent($user, "room:roomUsers", [
        $users
      ])
    );

    if ($user !== null) {
      $serializedUser = $this->serializeUser($user);
      $this->eventDispatcher->dispatch(
        SocketEvents::ROOM_RESPONSE,
        new RoomResponseEvent($room, "users:usersRepoAdd", [
          $serializedUser
        ])
      );
      $this->eventDispatcher->dispatch(
        SocketEvents::ROOM_RESPONSE,
        new RoomResponseEvent($room, "room:roomJoined", [
          $serializedUser
        ])
      );

      $this->eventDispatcher->dispatch(
        SocketEvents::USER_RESPONSE,
        new UserResponseEvent($user, "user:userRoles", [
          $user->getRoles()
        ])
      );
      $this->eventDispatcher->dispatch(
        SocketEvents::USER_RESPONSE,
        new UserResponseEvent($user, "room:roomMessage", [
          [
            "type"    => "joinMessage",
            "id"      => $this->nextNoticeID(),
            "date"    => new \DateTime(),
            "message" => $room->getSettings()->getJoinMessage()
          ]
        ])
      );
      $this->eventDispatcher->dispatch(
        SocketEvents::USER_RESPONSE,
        new UserResponseEvent($user, "room:roomMessage", [
          [
            "type"    => "notice",
            "id"      => $this->nextNoticeID(),
            "date"    => new \DateTime(),
            "message" => sprintf("%s joined the room", $user->getUsername())
          ]
        ])
      );
    }

    $this->flush($conn, $topic);
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user     = $this->getUser($conn);
    $username = null;
    if (!($user instanceof UserInterface)) {
      $username = $user;
      $user     = null;
    } else {
      $username = $user->getUsername();
    }
    unset($this->subs[$username]);

    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    $this->roomStorage->removeUser($room, $user);

    $this->eventDispatcher->dispatch(
      SocketEvents::ROOM_RESPONSE,
      new RoomResponseEvent($room, "room:roomParted", [
        $user->getUsername()
      ])
    );
    $this->eventDispatcher->dispatch(
      SocketEvents::ROOM_RESPONSE,
      new RoomResponseEvent($room, "room:roomMessage", [
        [
          "type"    => "notice",
          "id"      => $this->nextNoticeID(),
          "date"    => new \DateTime(),
          "message" => sprintf("%s left the room", $user->getUsername())
        ]
      ])
    );
  }

  /**
   * {@inheritdoc}
   *
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onPublish(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    $payload,
    array $exclude,
    array $eligible
  )
  {
    if (is_string($payload) && $payload === "ping") {
      $clientStorage = $this->container->get("app.ws.storage.driver");
      $clientStorage->lifeTime($conn->resourceId, 86400);
      return $this->dispatchToUser("room:pong", time())
        ->flush($conn, $topic);
    }

    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      return $this->logger->error("User not found.", $payload);
    }
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->getIsDeleted()) {
      return $this->logger->error("Room not found.", $payload);
    }

    // @see AppBundle\EventListener\Socket\SocketSubscriber
    return $this->eventDispatcher->dispatch(
      SocketEvents::ROOM_REQUEST,
      new RoomRequestEvent($room, $user, $payload)
    );
  }

  /**
   * @param RoomResponseEvent $event
   */
  public function onRoomResponse(RoomResponseEvent $event)
  {
    $topic = $this->rooms[$event->getRoom()->getName()];
    $topic->broadcast([
      "dispatch" => [
        ["action" => $event->getAction(), "args" => $event->getArgs()]
      ]
    ]);
  }

  /**
   * @param UserResponseEvent $event
   */
  public function onUserResponse(UserResponseEvent $event)
  {
    $username = $event->getUser()->getUsername();
    if ($package = $this->subs[$username]) {
      /** @var ConnectionInterface $conn */
      /** @var Topic $topic */
      $conn  = $package["conn"];
      $topic = $package["topic"];
      $conn->event($topic->getId(), [
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]);
    }
  }

  /**
   * @return int
   */
  private function nextNoticeID()
  {
    $this->noticeID++;
    return $this->noticeID;
  }
}
