<?php
namespace AppBundle\Topic;

use AppBundle\Entity\PrivateMessage;
use AppBundle\Entity\User;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Security\Core\User\UserInterface;

class PrivateMessageTopic extends AbstractTopic
{
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
  public function onPublish(ConnectionInterface $conn, Topic $topic, WampRequest $req, $event, array $exclude, array $eligible)
  {
    $this->logger->info("Got command " . $event["cmd"]);

    $payload = $event["message"];
    if (empty($payload["message"])) {
      $this->logger->error("Missing 'message' parameter.");
      return;
    }
    if (empty($payload["to"])) {
      $this->logger->error("Missing 'to' parameter.");
      return;
    }
    $message = $this->sanitizeMessage($payload["message"]);
    if (empty($message)) {
      $this->logger->error("Empty 'message' parameter.");
      return;
    }

    /** @var User $fromUser */
    /** @var User $toUser */
    /** @var PrivateMessage $pm */
    $fromUser = $this->getUser($conn);
    if (!($fromUser instanceof UserInterface)) {
      $this->logger->error("From user not found.", $event);
      return;
    }
    $toUser = $this->em->getRepository("AppBundle:User")
      ->findByUsername($payload["to"]);
    if (!($toUser instanceof UserInterface)) {
      $conn->event($topic->getId(), [
        "cmd"   => PrivateMessageCommands::ERROR,
        "error" => "User ${payload['to']} not found."
      ]);
      return;
    }
    $pm = (new PrivateMessage())
      ->setFromUser($fromUser)
      ->setToUser($toUser)
      ->setMessage($message);
    $pm = $this->em->merge($pm);
    $this->em->flush();

    $toUserConn = $this->clientManipulator->findByUsername($topic, $toUser->getUsername());
    if (!$toUserConn) {
      $this->logger->debug("To user is not online.", $event);
      return;
    }

    $message = $this->serializePrivateMessage($pm);
    $msg = [
      "cmd"     => PrivateMessageCommands::RECEIVE,
      "message" => $message
    ];
    $topic->broadcast($msg, [], [$toUserConn['connection']->WAMP->sessionId]);
    $conn->event($topic->getId(), [
      "cmd"     => PrivateMessageCommands::SENT,
      "message" => $message
    ]);
  }
}
