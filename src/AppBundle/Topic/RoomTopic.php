<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

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
   */
  public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($connection);
    if (!($user instanceof UserInterface)) {
      $user = null;
    }
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    if ($user) {
      $this->roomStorage->addUser($room, $user);
    }

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
      if ($u instanceof UserInterface) {
        $users[] = $u->getUsername();
        if (!in_array($u->getUsername(), $repoFound)) {
          $repoUsers[] = $this->serializeUser($u);
          $repoFound[] = $u->getUsername();
        }
      }
    }

    $connection->event($topic->getId(), [
      "cmd"      => RoomCommands::SETTINGS,
      "settings" => [
        "user" => [
          "showNotices" => true
        ],
        "site" => [],
        "room" => $this->serializeRoomSettings($room->getSettings())
      ]
    ]);
    $connection->event($topic->getId(), [
      "cmd"      => RoomCommands::MESSAGES,
      "messages" => array_reverse($this->serializeMessages($messages))
    ]);
    $connection->event($topic->getId(), [
      "cmd"   => RoomCommands::REPO_USERS,
      "users" => $repoUsers
    ]);
    $connection->event($topic->getId(), [
      "cmd"   => RoomCommands::USERS,
      "users" => $users
    ]);

    if ($user !== null) {
      $topic->broadcast([
        "cmd"  => RoomCommands::JOINED,
        "user" => $this->serializeUser($user)
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
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
    array $eligible)
  {
    $this->logger->info("Got command " . $event["cmd"], $event);
    $event = array_map("trim", $event);
    if (empty($event["cmd"])) {
      $this->logger->error("cmd not set.", $event);
      return;
    }
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      $this->logger->error("User not found.", $event);
      return;
    }
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->isDeleted()) {
      $this->logger->error("Room not found.", $event);
      return;
    }

    switch ($event["cmd"]) {
      case RoomCommands::SEND:
        $this->handleSend($conn, $topic, $req, $room, $user, $event);
        break;
    }
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
}
