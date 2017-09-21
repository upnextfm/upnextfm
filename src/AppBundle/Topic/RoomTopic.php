<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
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

      $conn->event($topic->getId(), [
        "cmd"      => RoomCommands::SETTINGS,
        "settings" => [
          "user" => $this->serializeUserSettings($settings),
          "site" => $this->container->getParameter("app_site_settings"),
          "room" => $this->serializeRoomSettings($room->getSettings())
        ]
      ]);
      $conn->event($topic->getId(), [
        "cmd"      => RoomCommands::MESSAGES,
        "messages" => array_reverse($this->serializeMessages($messages))
      ]);
      $conn->event($topic->getId(), [
        "cmd"   => RoomCommands::REPO_USERS,
        "users" => $repoUsers
      ]);
      $conn->event($topic->getId(), [
        "cmd"   => RoomCommands::USERS,
        "users" => $users
      ]);

      if ($user !== null) {
        $conn->event($topic->getId(), [
          "cmd"   => RoomCommands::ROLES,
          "roles" => $user->getRoles()
        ]);
        $topic->broadcast([
          "cmd"  => RoomCommands::JOINED,
          "user" => $this->serializeUser($user)
        ]);
      }
    } catch (Exception $e) {
      $this->handleError($e);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
    try {
      $user = $this->getUser($connection);
      if (!($user instanceof UserInterface)) {
        return;
      }
      $room = $this->getRoom($request->getAttributes()->get("room"), $user);
      $this->roomStorage->removeUser($room, $user);
      $topic->broadcast([
        "cmd"      => RoomCommands::PARTED,
        "username" => $user->getUsername()
      ]);
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
    array $eligible)
  {
    try {
      if (is_string($event) && $event === "ping") {
        $clientStorage = $this->container->get("app.ws.storage.driver");
        $clientStorage->lifeTime($conn->resourceId, 86400);
        return $conn->event($topic->getId(), "pong");
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
    array $event)
  {
    $message = $this->sanitizeMessage($event["message"]);
    if (empty($message)) {
      return;
    }

    /** @var ChatLog $chatLog */
    $chatLog = new ChatLog($room, $user, $message);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();
    $topic->broadcast([
      "cmd"     => RoomCommands::SEND,
      "message" => $this->serializeMessage($chatLog)
    ]);
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
    array $event)
  {
    $message = $this->sanitizeMessage($event["message"]);
    if (empty($message)) {
      return;
    }

    /** @var ChatLog $chatLog */
    $chatLog = new ChatLog($room, $user, $message);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();
    $topic->broadcast([
      "cmd"     => RoomCommands::ME,
      "message" => $this->serializeMessage($chatLog, "me")
    ]);
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
    array $event)
  {
    if (!isset($event["settings"])) {
      $event["settings"] = [];
    }
    $event["settings"]["showNotices"] = isset($event["settings"]["showNotices"])
      ? $event["settings"]["showNotices"]
      : true;
    $event["settings"]["textColor"] = isset($event["settings"]["textColor"])
      ? $event["settings"]["textColor"]
      : "#FFFFFF";

    $settings = $user->getSettings();
    if (!$settings) {
      $settings = new UserSettings();
      $settings->setUser($user);
      $user->setSettings($settings);
    }
    $settings->setShowNotices($event["settings"]["showNotices"]);
    $settings->setTextColor($event["settings"]["textColor"]);
    $this->em->flush();
  }
}
