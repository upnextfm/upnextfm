<?php
namespace AppBundle\Topic;

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

    $username = $user->getUsername();
    $topic->broadcast([
      "cmd"  => Commands::JOIN,
      "user" => [
        "username" => $username,
        "avatar"   => "https://api.adorable.io/avatars/50/${username}%40upnext.fm",
        "profile"  => "https://upnext.fm/u/${username}",
        "roles"    => ["user"]
      ]
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
      "cmd"  => Commands::LEAVE,
      "username" => $user->getUsername()
    ]);
  }

  /**
   * This will receive any Publish requests for this topic.
   *
   * @param ConnectionInterface $connection
   * @param Topic $topic
   * @param WampRequest $request
   * @param $event
   * @param array $exclude
   * @param array $eligible
   * @return mixed|void
   */
  public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
  {
    $user = $this->getUser($connection);
    if (!($user instanceof UserInterface)) {
      return;
    }

    $topic->broadcast([
      'cmd' => Commands::SEND,
      'msg' => [
        "id"      => rand(100, 500),
        "date"    => $event["date"],
        "from"    => $user->getUsername(),
        "message" => $event["msg"]
      ],
    ]);
  }
}
