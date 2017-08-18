<?php
namespace AppBundle\Topic;

use AppBundle\Entity\ChatLog;
use Doctrine\ORM\EntityManagerInterface;
use Gos\Bundle\WebSocketBundle\Client\Auth\WebsocketAuthenticationProviderInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Ratchet\Wamp\WampConnection;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

abstract class AbstractTopic implements TopicInterface
{
  /**
   * @var ClientManipulatorInterface
   */
  protected $clientManipulator;

  /**
   * @var JWTTokenAuthenticator
   */
  protected $tokenAuthenticator;

  /**
   * @var UserProviderInterface
   */
  protected $userProvider;

  /**
   * @var WebsocketAuthenticationProviderInterface
   */
  protected $authenticationProvider;

  /**
   * @var EntityManagerInterface
   */
  protected $em;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * @param ClientManipulatorInterface $clientManipulator
   * @param JWTTokenAuthenticator $tokenAuthenticator
   * @param UserProviderInterface $userProvider
   * @param WebsocketAuthenticationProviderInterface $authenticationProvider
   * @param EntityManagerInterface $em
   * @param LoggerInterface $logger
   */
  public function __construct(
    ClientManipulatorInterface $clientManipulator,
    JWTTokenAuthenticator $tokenAuthenticator,
    UserProviderInterface $userProvider,
    WebsocketAuthenticationProviderInterface $authenticationProvider,
    EntityManagerInterface $em,
    LoggerInterface $logger)
  {
    $this->clientManipulator      = $clientManipulator;
    $this->tokenAuthenticator     = $tokenAuthenticator;
    $this->userProvider           = $userProvider;
    $this->authenticationProvider = $authenticationProvider;
    $this->logger                 = $logger;
    $this->em                     = $em;
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
/*    $topic->broadcast([
      'cmd' => Commands::JOIN,
      'msg' => $connection->resourceId . " has joined " . $topic->getId()
    ]);*/
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
/*    $topic->broadcast([
      'cmd' => Commands::LEAVE,
      'msg' => $connection->resourceId . " has left " . $topic->getId()
    ]);*/
  }

  /**
   * Authenticates the user
   *
   * @param ConnectionInterface $connection
   * @param string $token
   * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
   */
  protected function authenticate(ConnectionInterface $connection, $token)
  {
    $connection->WebSocket->request->getQuery()->set("token", $token);
    return $this->authenticationProvider->authenticate($connection);
  }

  /**
   * @param ConnectionInterface $connection
   * @param array $event
   * @return UserInterface
   */
  protected function getUser(ConnectionInterface $connection, array $event = [])
  {
    if (empty($event["token"])) {
      return $this->clientManipulator->getClient($connection);
    }

    $request = new Request();
    $request->headers->set('Authorization', 'Bearer ' . $event["token"]);
    $creds = $this->tokenAuthenticator->getCredentials($request);
    if (!$creds) {
      return $this->clientManipulator->getClient($connection);
    }

    return $this->tokenAuthenticator->getUser($creds, $this->userProvider);
  }

  /**
   * @param UserInterface $user
   * @return array
   */
  protected function serializeUser(UserInterface $user)
  {
    $username = $user->getUsername();
    return [
      "username" => $username,
      "avatar"   => "https://robohash.org/${username}?set=set3",
      "profile"  => "https://upnext.fm/u/${username}",
      "roles"    => $user->getRoles()
    ];
  }

  /**
   * @param ChatLog[] $messages
   * @return array
   */
  protected function serializeMessages($messages)
  {
    $serialized = [];
    foreach($messages as $message) {
      if ($message && $message->getUser()) {
        $serialized[] = [
          "id"      => $message->getId(),
          "date"    => $message->getDateCreated()->format("D M d Y H:i:s O"),
          "from"    => $message->getUser()->getUsername(),
          "message" => $message->getMessage()
        ];
      }
    }

    return $serialized;
  }
}
