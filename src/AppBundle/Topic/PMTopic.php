<?php
namespace AppBundle\Topic;

use AppBundle\Entity\PrivateMessage;
use AppBundle\Entity\User;
use AppBundle\EventListener\Socket\PMRequestEvent;
use AppBundle\EventListener\Socket\PMResponseEvent;
use AppBundle\EventListener\Socket\RoomRequestEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampConnection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PMTopic extends AbstractTopic implements EventSubscriberInterface
{
  /**
   * @var array
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
      $this->users[$username] = ["conn" => $conn, "topic" => $topic];
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
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $event, array $exclude, array $eligible)
  {
    if (!isset($event["dispatch"])) {
      return $this->logger->error("Invalid payload.", $event);
    }
    $user = $this->getUser($conn);
    if (!($user instanceof UserInterface)) {
      return $this->logger->error("User not found.", $event);
    }

    // @see AppBundle\EventListener\Socket\SocketSubscriber
    return $this->eventDispatcher->dispatch(
      SocketEvents::PM_REQUEST,
      new PMRequestEvent($user, $event)
    );
  }

  /**
   * @param PMResponseEvent $event
   */
  public function onPMResponse(PMResponseEvent $event)
  {
    /** @var ConnectionInterface|WampConnection $conn */
    /** @var Topic $topic */

    $toUser     = $event->getUser();
    $toUsername = $toUser->getUsername();
    if (isset($this->users[$toUsername])) {
      $conn  = $this->users[$toUsername]["conn"];
      $topic = $this->users[$toUsername]["topic"];
      $conn->event($topic->getId(), [
        "dispatch" => [
          ["action" => $event->getAction(), "args" => $event->getArgs()]
        ]
      ]);
    }

    /*    $toUserConn = $this->clientManipulator->findByUsername($topic, $toUser->getUsername());
        if (!$toUserConn) {
          $this->logger->debug("To user is not online.", $event);
          return true;
        }*/
  }
}
