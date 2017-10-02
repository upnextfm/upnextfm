<?php
namespace AppBundle\Topic;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\EventListener\Socket\PMRequestEvent;
use AppBundle\EventListener\Socket\PMResponseEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\Wamp\WampConnection;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;

/**
 * Dispatches private message related frontend commands to backend event listeners.
 *
 * @see AppBundle\EventListener\Socket\PMListener
 */
class PMTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var Subscriber[]
   */
  private $subs = [];

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
   * Listens for responses which should be sent to a specific user
   *
   * @outgoing
   * @param PMResponseEvent $event
   */
  public function onPMResponse(PMResponseEvent $event)
  {
    $toUser     = $event->getUser();
    $toUsername = $toUser->getUsername();
    if (isset($this->subs[$toUsername])) {
      $conn  = $this->subs[$toUsername]->getConnection();
      $topic = $this->subs[$toUsername]->getTopic();
      $conn->event($topic->getId(), [
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]);
    }
  }

  /**
   * Called when a user joins the room
   *
   * {@inheritdoc}
   *
   * @incoming
   * @param ConnectionInterface|WampConnection $conn
   */
  public function onSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    // Save the connection and topic for the user so we can access
    // them later by username.
    $user = $this->getUser($conn);
    if ($user instanceof UserInterface) {
      $username = $user->getUsername();
      if (isset($this->subs[$username])) {
        unset($this->subs[$username]);
      }
      $this->subs[$username] = new Subscriber($conn, $topic);
    }
  }

  /**
   * Called when a user leaves the room
   *
   * {@inheritdoc}
   *
   * @incoming
   */
  public function onUnSubscribe(ConnectionInterface $conn, Topic $topic, WampRequest $request)
  {
    // Remove the user from the list of subscribers.
    $user = $this->getUser($conn);
    if ($user instanceof UserInterface) {
      unset($this->subs[$user->getUsername()]);
    }
  }

  /**
   * Called when a user sends a command to the room
   *
   * {@inheritdoc}
   *
   * @incoming
   */
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $payload, array $ex, array $el)
  {
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
}
