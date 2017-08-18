<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
use FOS\UserBundle\Model\UserInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampConnection;

class RoomTopic extends AbstractTopic
{
  /**
   * Like RPC is will use to prefix the channel
   *
   * @return string
   */
  public function getName()
  {
    return "room.topic";
  }

  /**
   * This will receive any Subscription requests for this topic.
   *
   * @param ConnectionInterface|WampConnection $connection
   * @param Topic $topic
   * @param WampRequest $request
   * @return void
   */
  public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($connection);
    if (!($user instanceof UserInterface)) {
      return;
    }

    $room     = $this->getRoom($request->getAttributes()->get("room"), $user);
    $repo     = $this->em->getRepository("AppBundle:ChatLog");
    $messages = $repo->findRecent($room, 20);


    foreach($messages as $message) {

    }

    $users = [];
    foreach($topic as $client) {
      $u = $this->getUser($client);
      if ($u instanceof UserInterface) {
        $users[] = $u->getUsername();
      }
    }

    $topic->broadcast([
      "cmd"  => Commands::JOINED,
      "user" => $this->serializeUser($user)
    ]);
    $connection->event($topic->getId(), [
      "cmd"      => Commands::MESSAGES,
      "messages" => $this->serializeMessages($messages)
    ]);
    $connection->event($topic->getId(), [
      "cmd"   => Commands::USERS,
      "users" => $users
    ]);
  }

  /**
   * This will receive any UnSubscription requests for this topic.
   *
   * @param ConnectionInterface|WampConnection $connection
   * @param Topic $topic
   * @param WampRequest $request
   * @return void
   */
  public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($connection);
    if (!($user instanceof UserInterface)) {
      return;
    }

    $topic->broadcast([
      "cmd"      => Commands::PARTED,
      "username" => $user->getUsername()
    ]);
  }

  /**
   * This will receive any Publish requests for this topic.
   *
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param $event
   * @param array $exclude
   * @param array $eligible
   * @return mixed|void
   */
  public function onPublish(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    $event,
    array $exclude,
    array $eligible)
  {
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      return;
    }
    $room = $this->getRoom($req->getAttributes()->get("room"), $user);
    if (!$room || $room->isDeleted()) {
      return;
    }

    switch($event["cmd"]) {
      case Commands::SEND:
        $this->handleSend($conn, $topic, $req, $room, $user, $event);
        break;
    }
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param Room $room
   * @param UserInterface $user
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

    $msg = trim($event["msg"]);
    if (empty($msg)) {
      return;
    }

    $chatLog = new ChatLog($room, $user, $msg);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();

    $topic->broadcast([
      'cmd' => Commands::SEND,
      'msg' => [
        "id"      => $chatLog->getId(),
        "date"    => $event["date"],
        "from"    => $user->getUsername(),
        "message" => $msg
      ],
    ]);
  }

  /**
   * @param string $roomName
   * @param UserInterface $user
   * @return Room
   */
  protected function getRoom($roomName, UserInterface $user = null)
  {
    $repo = $this->em->getRepository("AppBundle:Room");
    $room = $repo->findByName($roomName);
    if (!$room && $user !== null) {
      $room = new Room($roomName, $user);
      $this->em->merge($room);
      $this->em->flush();
    }

    return $room;
  }
}
