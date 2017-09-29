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
      SocketEvents::ROOM_RESPONSE => "onRoomResponse"
    ];
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
   * {@inheritdoc}
   *
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    try {
      /** @var User $user */
      $user = $this->getUser($conn);
      if (!($user instanceof UserInterface)) {
        $user = null;
      }
      $room = $this->getRoom($request->getAttributes()->get("room"), $user);
      if ($user) {
        $this->roomStorage->addUser($room, $user);
      }

      // Save the client connection and room index.
      $client = ["conn" => $conn, "topic" => $topic];
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

      $this->dispatchToUser("settings:settingsAll", [
        "site" => $this->container->getParameter("app_site_settings"),
        "user" => $this->serializeUserSettings($settings),
        "room" => $this->serializeRoomSettings($room->getSettings())
      ]);

      $this->dispatchToUser(
        "room:roomMessages",
        array_reverse($this->serializeMessages($messages))
      );
      $this->dispatchToUser(
        "users:usersRepoAddMulti",
        $repoUsers
      );
      $this->dispatchToUser(
        "room:roomUsers",
        $users
      );

      if ($user !== null) {
        $serializedUser = $this->serializeUser($user);
        $this->dispatchToRoom(
          "users:usersRepoAdd",
          $serializedUser
        );
        $this->dispatchToRoom(
          "room:roomJoined",
          $serializedUser
        );
        $this->dispatchToUser(
          "user:userRoles",
          $user->getRoles()
        );
        $this->dispatchToUser(
          "room:roomMessage",
          [
            "type"    => "joinMessage",
            "id"      => $this->nextNoticeID(),
            "date"    => new \DateTime(),
            "message" => $room->getSettings()->getJoinMessage()
          ]
        );
        $this->dispatchToRoomOnly(
          "room:roomMessage",
          [
            "type"    => "notice",
            "id"      => $this->nextNoticeID(),
            "date"    => new \DateTime(),
            "message" => sprintf("%s joined the room", $user->getUsername())
          ]
        );
      }

      $this->flush($conn, $topic);
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    try {
      $user = $this->getUser($conn);
      if (!($user instanceof UserInterface)) {
        return;
      }
      $room = $this->getRoom($request->getAttributes()->get("room"), $user);
      $this->roomStorage->removeUser($room, $user);

      // Remove the client connection and room index.
      $roomName = $room->getName();
      if (!isset($this->subs[$roomName])) {
        $this->subs[$roomName] = [];
      }
      if (!empty($this->subs[$roomName])) {
        $client = ["conn" => $conn, "topic" => $topic];
        $index = array_search($client, $this->subs[$roomName]);
        if (false !== $index) {
          unset($this->subs[$roomName][$index]);
          if (count($this->subs[$roomName]) === 0) {
            unset($this->subs[$roomName]);
            unset($this->rooms[$roomName]);
          }
        }
      }

      $this->dispatchToRoom(
        "room:roomParted",
        $user->getUsername()
      );
      $this->dispatchToRoomOnly(
        "room:roomMessage",
        [
          "type"    => "notice",
          "id"      => $this->nextNoticeID(),
          "date"    => new \DateTime(),
          "message" => sprintf("%s left the room", $user->getUsername())
        ]
      );
      $this->flush($conn, $topic);
    } catch (Exception $e) {
      $this->handleError($e);
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
    $event,
    array $exclude,
    array $eligible
  )
  {
    try {
      if (is_string($event) && $event === "ping") {
        $clientStorage = $this->container->get("app.ws.storage.driver");
        $clientStorage->lifeTime($conn->resourceId, 86400);
        return $this->dispatchToUser("room:pong", time())
          ->flush($conn, $topic);
      }

      if (!isset($event["dispatch"])) {
        return $this->logger->error("Invalid payload.", $event);
      }
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
        SocketEvents::ROOM_REQUEST,
        new RoomRequestEvent($room, $user, $event)
      );
    } catch (Exception $e) {
      return $this->handleError($e);
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
