<?php
namespace AppBundle\Topic;

use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class AuthTopic extends AbstractTopic
{
  /**
   * Like RPC is will use to prefix the channel
   *
   * @return string
   */
  public function getName()
  {
    return "auth.topic";
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
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $event, array $exclude, array $eligible)
  {
    if ($event["cmd"] === Commands::AUTH) {
      $this->authenticate($conn, $event["token"]);
    }
  }
}
