<?php
namespace AppBundle\Topic;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\EventListener\Socket\PMRequestEvent;
use AppBundle\EventListener\Socket\PMResponseEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use AppBundle\Entity\User;
use Ratchet\Wamp\WampConnection;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

class PMTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var Subscriber[]
   */
  private $users = [];

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return "pms.topic";
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      SocketEvents::PM_RESPONSE => "onPMResponse"
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    /** @var User $user */
    $user = $this->getUser($conn);
    if ($user instanceof UserInterface) {
      $username = $user->getUsername();
      if (isset($this->users[$username])) {
        unset($this->users[$username]);
      }
      $this->users[$username] = new Subscriber($conn, $topic);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    /** @var User $user */
    $user = $this->getUser($conn);
    if ($user instanceof UserInterface) {
      unset($this->users[$user->getUsername()]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $payload, array $exclude, array $eligible)
  {
    if (!isset($payload["dispatch"])) {
      return $this->logger->error("Invalid payload.", $payload);
    }
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      return $this->logger->error("User not found.", $payload);
    }

    // @see AppBundle\EventListener\Socket\SocketSubscriber
    return $this->eventDispatcher->dispatch(
      SocketEvents::PM_REQUEST,
      new PMRequestEvent($user, $payload)
    );
  }

  /**
   * @param PMResponseEvent $event
   */
  public function onPMResponse(PMResponseEvent $event)
  {
    $toUser     = $event->getUser();
    $toUsername = $toUser->getUsername();
    if (isset($this->users[$toUsername])) {
      $conn  = $this->users[$toUsername]->getConnection();
      $topic = $this->users[$toUsername]->getTopic();
      $conn->event($topic->getId(), [
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]);
    }
  }
}
