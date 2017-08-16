<?php
namespace AppBundle\Topic;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\Wamp\WampConnection;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RoomTopic implements TopicInterface
{
  /**
   * @var JWTTokenAuthenticator
   */
  protected $jwt;

  /**
   * @var UserProviderInterface
   */
  protected $userProvider;

  /**
   * @param JWTTokenAuthenticator $jwt
   * @param UserProviderInterface $userProvider
   */
  public function __construct(JWTTokenAuthenticator $jwt, UserProviderInterface $userProvider)
  {
    $this->jwt          = $jwt;
    $this->userProvider = $userProvider;
  }

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
    //this will broadcast the message to ALL subscribers of this topic.
    $topic->broadcast([
      'cmd' => Commands::JOIN,
      'msg' => $connection->resourceId . " has joined " . $topic->getId()
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
    //this will broadcast the message to ALL subscribers of this topic.
    $topic->broadcast([
      'cmd' => Commands::LEAVE,
      'msg' => $connection->resourceId . " has left " . $topic->getId()
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
    /*
    $httpRequest = new Request();
    $httpRequest->headers->set('Authorization', 'Bearer ' . $event["token"]);
    $creds = $this->jwt->getCredentials($httpRequest);
    $user = $this->jwt->getUser($creds, $this->userProvider);
    print_r($user);
*/
    //echo $request->getAttributes()->get('room');
    $topic->broadcast([
      'cmd' => Commands::SEND,
      'msg' => [
        "id"      => rand(100, 500),
        "date"    => $event["date"],
        "from"    => "headzoo",
        "message" => $event["msg"]
      ],
    ]);
  }
}
