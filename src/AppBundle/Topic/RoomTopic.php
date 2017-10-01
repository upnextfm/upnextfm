<?php
namespace AppBundle\Topic;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Entity\ChatLog;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSettings;
use AppBundle\EventListener\Socket\RoomActions;
use AppBundle\EventListener\Socket\RoomRequestEvent;
use AppBundle\EventListener\Socket\SettingsActions;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\RoomResponseEvent;
use AppBundle\EventListener\Socket\UserActions;
use AppBundle\EventListener\Socket\UserResponseEvent;
use AppBundle\EventListener\Socket\UsersActions;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampConnection;
use Ratchet\Wamp\Topic;

class RoomTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var int
   */
  protected $noticeID = 0;

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
    $username = $user->getUsername();
    $room     = $this->getRoom($request->getAttributes()->get("room"), $user);
    $roomName = $room->getName();

    $this->roomStorage->addUser($room, $user);
    $this->subs[$username]  = new Subscriber($conn, $topic);
    $this->rooms[$roomName] = $topic;

    $repo      = $this->em->getRepository("AppBundle:ChatLog");
    $messages  = $repo->findRecent($room, $this->getParameter("app_room_recent_messages_count"));
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
      if (!$u->getIsAnonymous()) {
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

    $this->dispatchToUser($user, SettingsActions::ALL, [
      [
        "site" => $this->container->getParameter("app_site_settings"),
        "user" => $this->serializeUserSettings($settings),
        "room" => $this->serializeRoomSettings($room->getSettings())
      ]
    ]);
    $this->dispatchToUser($user, RoomActions::MESSAGES, [
      array_reverse($this->serializeMessages($messages))
    ]);
    $this->dispatchToUser($user, UsersActions::REPO_ADD_MULTI, [
      $repoUsers
    ]);
    $this->dispatchToUser($user, RoomActions::USERS, [
      $users
    ]);
    $this->dispatchToUser($user, UserActions::ROLES, [
      $user->getRoles()
    ]);
    $this->dispatchToUser($user, RoomActions::MESSAGE, [
      [
        "type"    => "joinMessage",
        "id"      => $this->nextNoticeID(),
        "date"    => new \DateTime(),
        "message" => $room->getSettings()->getJoinMessage()
      ]
    ]);

    if (!$user->getIsAnonymous()) {
      $serializedUser = $this->serializeUser($user);
      $this->dispatchToRoom($room, UsersActions::REPO_ADD, [
        $serializedUser
      ]);
      $this->dispatchToRoom($room, RoomActions::JOINED, [
        $serializedUser
      ]);
      $this->dispatchToRoom($room, RoomActions::MESSAGE, [
        [
          "type"    => "notice",
          "id"      => $this->nextNoticeID(),
          "date"    => new \DateTime(),
          "message" => sprintf("%s joined the room", $user->getUsername())
        ]
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    $user     = $this->getUser($conn);
    $username = $user->getUsername();
    unset($this->subs[$username]);

    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    $this->roomStorage->removeUser($room, $user);

    if (!$user->getIsAnonymous()) {
      $this->dispatchToRoom($room, RoomActions::PARTED, [
        $user->getUsername()
      ]);
      $this->dispatchToRoom($room, RoomActions::MESSAGE, [
        [
          "type"    => "notice",
          "id"      => $this->nextNoticeID(),
          "date"    => new \DateTime(),
          "message" => sprintf("%s left the room", $user->getUsername())
        ]
      ]);
    }
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
    $user = $this->getUser($conn);
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->getIsDeleted()) {
      return $this->logger->error("Room not found.", $payload);
    }

    if (is_string($payload) && $payload === "ping") {
      $clientStorage = $this->container->get("app.ws.storage.driver");
      $clientStorage->lifeTime($conn->resourceId, 86400);
      return $this->dispatchToUser($user, RoomActions::PONG, [
        time()
      ]);
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
   * @return int
   */
  private function nextNoticeID()
  {
    $this->noticeID++;
    return $this->noticeID;
  }
}
