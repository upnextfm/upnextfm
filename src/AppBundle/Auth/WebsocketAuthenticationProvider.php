<?php
namespace AppBundle\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Gos\Bundle\WebSocketBundle\Client\Exception\StorageException;
use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Ratchet\Wamp\WampConnection;
use Ratchet\ConnectionInterface;
use Psr\Log\LoggerInterface;

class WebsocketAuthenticationProvider
  extends \Gos\Bundle\WebSocketBundle\Client\Auth\WebsocketAuthenticationProvider
{
  /**
   * @var JWTTokenAuthenticator
   */
  protected $tokenAuthenticator;

  /**
   * @var UserProviderInterface
   */
  protected $userProvider;

  /**
   * @param TokenStorageInterface $tokenStorage
   * @param array $firewalls
   * @param ClientStorageInterface $clientStorage
   * @param JWTTokenAuthenticator $tokenAuthenticator
   * @param UserProviderInterface $userProvider
   * @param LoggerInterface $logger
   */
  public function __construct(
    $tokenStorage,
    $firewalls = array(),
    ClientStorageInterface $clientStorage,
    JWTTokenAuthenticator $tokenAuthenticator,
    UserProviderInterface $userProvider,
    LoggerInterface $logger = null
  ) {
    parent::__construct($tokenStorage, $firewalls, $clientStorage, $logger);
    $this->tokenAuthenticator = $tokenAuthenticator;
    $this->userProvider       = $userProvider;
  }

  /**
   * @param ConnectionInterface|WampConnection $conn
   *
   * @return TokenInterface
   *
   * @throws StorageException
   * @throws \Exception
   */
  public function authenticate(ConnectionInterface $conn)
  {
    if ($jwt = $conn->WebSocket->request->getQuery()->get("token")) {
      $request = new Request();
      $request->headers->set("Authorization", "Bearer " . $jwt);

      if ($token = $this->tokenAuthenticator->getCredentials($request)) {
        $user          = $this->tokenAuthenticator->getUser($token, $this->userProvider);
        $username      = $user instanceof UserInterface ? $user->getUsername() : $user;
        $loggerContext = [
          "connection_id" => $conn->resourceId,
          "session_id"    => $conn->WAMP->sessionId,
        ];

        try {
          $identifier = $this->clientStorage->getStorageId($conn, $username);
        } catch (StorageException $e) {
          $this->logger->error(
            $e->getMessage(),
            $loggerContext
          );
          throw $e;
        }

        $loggerContext["storage_id"] = $identifier;
        $this->clientStorage->addClient($identifier, $user);
        $conn->WAMP->clientStorageId = $identifier;
        $this->logger->info(sprintf(
          "%s connected",
          $username
        ), $loggerContext);

        return $token;
      }
    }

    return parent::authenticate($conn);
  }
}
