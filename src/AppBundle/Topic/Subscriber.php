<?php
namespace AppBundle\Topic;

use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampConnection;

class Subscriber
{
  /**
   * @var ConnectionInterface|WampConnection
   */
  protected $connection;

  /**
   * @var Topic
   */
  protected $topic;

  /**
   * Constructor
   *
   * @param ConnectionInterface $connection
   * @param Topic $topic
   */
  public function __construct(ConnectionInterface $connection, Topic $topic)
  {
    $this->connection = $connection;
    $this->topic      = $topic;
  }

  /**
   * @return ConnectionInterface|WampConnection
   */
  public function getConnection()
  {
    return $this->connection;
  }

  /**
   * @param ConnectionInterface|WampConnection $connection
   * @return Subscriber
   */
  public function setConnection($connection)
  {
    $this->connection = $connection;
    return $this;
  }

  /**
   * @return Topic
   */
  public function getTopic()
  {
    return $this->topic;
  }

  /**
   * @param Topic $topic
   * @return Subscriber
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
    return $this;
  }
}
