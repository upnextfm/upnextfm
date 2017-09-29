<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomSettings;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSettings;
use FOS\UserBundle\Model\UserInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Exception;
use Ratchet\Wamp\WampConnection;

class RoomTopic extends AbstractTopic
{
  /**
   * @var int
   */
  protected $noticeID = 0;

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return "room.topic";
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

      $this->logger->info("Got command " . $event["cmd"], $event);

      if (empty($event["cmd"])) {
        $this->logger->error("cmd not set.", $event);
        return true;
      }

      $user = $this->getUser($conn);
      if (!($user instanceof UserInterface)) {
        $this->logger->error("User not found.", $event);
        return true;
      }
      $room = $this->getRoom($req->getAttributes()->get("room"), $user);
      if (!$room || $room->getIsDeleted()) {
        $this->logger->error("Room not found.", $event);
        return true;
      }

      switch ($event["cmd"]) {
        case RoomCommands::SEND:
          $this->handleSend($conn, $topic, $req, $room, $user, $event);
          break;
        case RoomCommands::ME:
          $this->handleMe($conn, $topic, $req, $room, $user, $event);
          break;
        case RoomCommands::SAVE_SETTINGS:
          $this->handleSaveSettings($conn, $topic, $req, $room, $user, $event);
          break;
      }
    } catch (Exception $e) {
      $this->handleError($e);
    }

    return true;
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param Room $room
   * @param UserInterface|User $user
   * @param array $event
   */
  protected function handleSend(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    Room $room,
    UserInterface $user,
    array $event
  )
  {
    $message = $this->sanitizeMessage($event["message"]);
    if (empty($message)) {
      return;
    }

    /** @var ChatLog $chatLog */
    $chatLog = new ChatLog($room, $user, $message);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();

    // Dispatch.
    $this->dispatchToRoom(
      "room:roomMessage",
      $this->serializeMessage($chatLog)
    )->flush($conn, $topic);
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param Room $room
   * @param UserInterface|User $user
   * @param array $event
   */
  protected function handleMe(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    Room $room,
    UserInterface $user,
    array $event
  )
  {
    $message = $this->sanitizeMessage($event["message"]);
    if (empty($message)) {
      return;
    }

    /** @var ChatLog $chatLog */
    $chatLog = new ChatLog($room, $user, $message);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();

    // Dispatch.
    $this->dispatchToRoom(
      "room:roomMessage",
      $this->serializeMessage($chatLog, "me")
    )->flush($conn, $topic);
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param Room $room
   * @param UserInterface|User $user
   * @param array $event
   */
  protected function handleSaveSettings(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    Room $room,
    UserInterface $user,
    array $event
  )
  {
    if (!isset($event["settings"])) {
      $event["settings"] = [];
    }
    switch($event["type"]) {
      case "user":
        $this->saveUserSettings($user, $event["settings"]);
        break;
      case "room":
        $this->saveRoomSettings($room, $event["settings"]);
        break;
    }
  }

  /**
   * @param UserInterface|User $user
   * @param array $settings
   */
  private function saveUserSettings(UserInterface $user, array $settings)
  {
    $settings["showNotices"] = isset($settings["showNotices"])
      ? $settings["showNotices"]
      : true;
    $settings["textColor"] = isset($settings["textColor"])
      ? $settings["textColor"]
      : "#FFFFFF";

    $userSettings = $user->getSettings();
    if (!$userSettings) {
      $userSettings = new UserSettings();
      $userSettings->setUser($user);
      $user->setSettings($userSettings);
    }
    $userSettings->setShowNotices($settings["showNotices"]);
    $userSettings->setTextColor($settings["textColor"]);
    $this->em->flush();
  }

  /**
   * @param Room $room
   * @param array $settings
   */
  private function saveRoomSettings(Room $room, array $settings)
  {
    $settings["joinMessage"] = isset($settings["joinMessage"])
      ? $settings["joinMessage"]
      : "";

    $roomSettings = $room->getSettings();
    if (!$roomSettings) {
      $roomSettings = new RoomSettings();
      $roomSettings->setRoom($room);
      $room->setSettings($roomSettings);
    }

    $roomSettings->setJoinMessage($settings["joinMessage"]);
    $this->em->flush();
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
