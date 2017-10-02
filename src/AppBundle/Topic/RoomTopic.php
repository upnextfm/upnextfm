<?php
namespace AppBundle\Topic;


use AppBundle\Storage\SocketPredisDriver;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\EventListener\Socket\RoomActions;
use AppBundle\EventListener\Socket\RoomRequestEvent;
use AppBundle\EventListener\Socket\SettingsActions;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\RoomResponseEvent;
use AppBundle\EventListener\Socket\UserActions;
use AppBundle\EventListener\Socket\UserResponseEvent;
use AppBundle\EventListener\Socket\UsersActions;
use AppBundle\Entity\RoomSettings;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSettings;
use AppBundle\Storage\RoomStorage;
use AppBundle\Entity\ChatLog;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampConnection;
use Ratchet\Wamp\Topic;

/**
 * Dispatches room related frontend commands to backend event listeners.
 *
 * @see AppBundle\EventListener\Socket\RoomListener
 */
class RoomTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var int
   */
  protected $noticeID = 0;

  /**
   * @var array
   */
  protected $siteSettings = [];

  /**
   * @var Subscriber[]
   */
  protected $subs = [];

  /**
   * @var Topic[]
   */
  protected $rooms = [];

  /**
   * @var RoomStorage
   */
  protected $roomStorage;

  /**
   * @var SocketPredisDriver
   */
  protected $clientStorage;

  /**
   * @param array $siteSettings
   * @return $this
   */
  public function setSiteSettings(array $siteSettings)
  {
    $this->siteSettings = $siteSettings;
    return $this;
  }

  /**
   * @param RoomStorage $roomStorage
   * @return $this
   */
  public function setRoomStorage(RoomStorage $roomStorage)
  {
    $this->roomStorage = $roomStorage;
    return $this;
  }

  /**
   * @param SocketPredisDriver $clientStorage
   * @return $this
   */
  public function setClientStorage(SocketPredisDriver $clientStorage)
  {
    $this->clientStorage = $clientStorage;
    return $this;
  }

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
   * Listens for responses which should be sent to the whole room
   *
   * @outgoing
   * @param RoomResponseEvent $event
   * @return Topic|void
   */
  public function onRoomResponse(RoomResponseEvent $event)
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
   * Listens for responses which should be sent to a specific user in the room
   *
   * @outgoing
   * @param UserResponseEvent $event
   * @return WampConnection|void
   */
  public function onUserResponse(UserResponseEvent $event)
  {
    $username = $event->getUser()->getUsername();
    if (!isset($this->subs[$username])) {
      return $this->logger->error(sprintf(
        'User "%s" not subscribed to room.',
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
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    // Save the connection and topic for the user and room, so we can access
    // them later by username/name.
    $user = $this->getUser($conn);
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    $this->roomStorage->addUser($room, $user);
    $this->rooms[$room->getName()]     = $topic;
    $this->subs[$user->getUsername()]  = new Subscriber($conn, $topic);

    // Bootstrap the front end with the user's settings and roles.
    $this->dispatchToUser($user, UserActions::ROLES, [
      $user->getRoles()
    ]);
    $this->dispatchToUser($user, SettingsActions::ALL, [
      [
        "site" => $this->siteSettings,
        "user" => $this->serializeUserSettings($user->getSettings()),
        "room" => $this->serializeRoomSettings($room->getSettings())
      ]
    ]);

    // Get the chat buffer, e.g. the last 50 messages in the room.
    // The front end also needs a list of all users in the room, plus
    // all users who wrote a message found in the chat buffer.
    $users     = [];
    $repoFound = [];
    $repoUsers = [];
    $messages  = $this->chatLogRepository->findRecent($room, 100);

    foreach ($messages as $message) {
      $u = $message->getUser();
      if ($u && !in_array($u->getUsername(), $repoFound)) {
        $repoUsers[] = $this->serializeUser($message->getUser());
        $repoFound[] = $u->getUsername();
      }
    }
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

    // Send the user information and chat buffer to the client.
    $this->dispatchToUser($user, UsersActions::REPO_ADD_MULTI, [
      $repoUsers
    ]);
    $this->dispatchToUser($user, RoomActions::USERS, [
      $users
    ]);
    $this->dispatchToUser($user, RoomActions::MESSAGES, [
      array_reverse($this->serializeMessages($messages))
    ]);

    // Show the join message to the client. For non-anonymous users, add them
    // to the user's list, and show a "... has joined the room" message to
    // everyone in the room.
    $this->dispatchToUser($user, RoomActions::MESSAGE, [
      [
        "type"    => "joinMessage",
        "id"      => 1,
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
          "id"      => 2,
          "date"    => new \DateTime(),
          "message" => sprintf("%s joined the room", $user->getUsername())
        ]
      ]);
    }
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
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    $this->roomStorage->removeUser($room, $user);
    unset($this->subs[$user->getUsername()]);

    // Show the "... has left the room" to everyone in the room for
    // non-anonymous users. Also remove the user from the user's list.
    if (!$user->getIsAnonymous()) {
      $this->dispatchToRoom($room, RoomActions::PARTED, [
        $user->getUsername()
      ]);
      $this->dispatchToRoom($room, RoomActions::MESSAGE, [
        [
          "type"    => "notice",
          "id"      => 3,
          "date"    => new \DateTime(),
          "message" => sprintf("%s left the room", $user->getUsername())
        ]
      ]);
    }
  }

  /**
   * Called when a user sends a command to the room
   *
   * {@inheritdoc}
   *
   * @incoming
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $payload, array $ex, array $el)
  {
    $user = $this->getUser($conn);
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->getIsDeleted()) {
      return $this->logger->error("Room not found.", $payload);
    }

    if (is_string($payload) && $payload === "ping") {
      $this->clientStorage->lifeTime($conn->resourceId, 86400);
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
   * @param ChatLog $message
   * @param string $type
   * @return array
   */
  protected function serializeMessage(ChatLog $message, $type = "message")
  {
    return [
      "type"    => $type,
      "id"      => $message->getId(),
      "date"    => $message->getDateCreated()->format("D M d Y H:i:s O"),
      "from"    => $message->getUser()->getUsername(),
      "message" => $message->getMessage()
    ];
  }

  /**
   * @param ChatLog[] $messages
   * @return array
   */
  protected function serializeMessages($messages)
  {
    $serialized = [];
    foreach ($messages as $message) {
      $serialized[] = $this->serializeMessage($message);
    }

    return $serialized;
  }

  /**
   * @param RoomSettings $settings
   * @return array
   */
  protected function serializeRoomSettings(RoomSettings $settings)
  {
    return [
      "isPublic"    => $settings->isPublic(),
      "joinMessage" => $settings->getJoinMessage(),
      "thumbSm"     => $settings->getThumbSm(),
      "thumbMd"     => $settings->getThumbMd(),
      "thumbLg"     => $settings->getThumbLg()
    ];
  }

  /**
   * @param UserSettings $settings
   * @return array
   */
  protected function serializeUserSettings(UserSettings $settings)
  {
    return [
      "showNotices" => $settings->getShowNotices(),
      "textColor"   => $settings->getTextColor()
    ];
  }

  /**
   * @param UserInterface|User $user
   * @return array
   */
  protected function serializeUser(UserInterface $user)
  {
    $username = $user->getUsername();
    return [
      "username" => $username,
      "avatar"   => $user->getInfo()->getAvatarSm(),
      "profile"  => "https://upnext.fm/u/${username}",
      "roles"    => $user->getRoles()
    ];
  }
}
