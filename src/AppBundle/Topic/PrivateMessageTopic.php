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

    try {
      if (empty($event["cmd"])) {
        $this->logger->error("cmd not set.", $event);
        return true;
      }

      switch ($event["cmd"]) {
        case PrivateMessageCommands::SEND:
          $this->handleSend($conn, $topic, $req, $event);
          break;
        case PrivateMessageCommands::LOAD:
          $this->handleLoad($conn, $topic, $req, $event);
          break;
      }
    } catch (\Exception $e) {
      $this->handleError($e);
      return true;
    }

    return true;
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param array $event
   * @return $this|bool|mixed
   */
  protected function handleSend(ConnectionInterface $conn, Topic $topic, WampRequest $req, $event)
  {
    $payload = $event["message"];
    if (empty($payload["message"])) {
      $this->logger->error("Missing 'message' parameter.");
      return true;
    }
    if (empty($payload["to"])) {
      $this->logger->error("Missing 'to' parameter.");
      return true;
    }
    $message = $this->sanitizeMessage($payload["message"]);
    if (empty($message)) {
      $this->logger->error("Empty 'message' parameter.");
      return true;
    }

    /** @var User $fromUser */
    /** @var User $toUser */
    /** @var PrivateMessage $pm */
    $fromUser = $this->getUser($conn);
    if (!($fromUser instanceof UserInterface)) {
      $this->logger->error("From user not found.", $event);
      return true;
    }
    $toUser = $this->em->getRepository("AppBundle:User")
      ->findByUsername($payload["to"]);
    if (!($toUser instanceof UserInterface)) {
      return $this->dispatchError("User \"${payload['to']}\" not found.");
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
      return true;
    }

    // Dispatch
    $message = $this->serializePrivateMessage($pm);
    $this->dispatch(
      $toUserConn["connection"],
      "pms:pmsReceive",
      $message
    );
    $this->dispatchToUser(
      "pms:pmsSent",
      $message
    );

    return $this->flush($conn, $topic);
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param array $event
   * @return mixed|void
   */
  protected function handleLoad(ConnectionInterface $conn, Topic $topic, WampRequest $req, $event)
  {
    /** @var User $fromUser */
    /** @var User $toUser */
    /** @var PrivateMessage $pm */
    $fromUser = $this->getUser($conn);
    if (!($fromUser instanceof UserInterface)) {
      $this->logger->error("From user not found.", $event);
      return true;
    }
    $toUser = $this->em->getRepository("AppBundle:User")
      ->findByUsername($event["username"]);
    if (!($toUser instanceof UserInterface)) {
      return $this->dispatchError("User \"${event['username']}\" not found.");
    }

    $conversation = [];
    $repo = $this->em->getRepository("AppBundle:PrivateMessage");
    foreach ($repo->fetchConversation($fromUser, $toUser, 50) as $row) {
      $conversation[] = $this->serializePrivateMessage($row);
    }

    return $this->dispatchToUser(
      "pms:pmsLoad",
      $toUser->getUsername(),
      array_reverse($conversation)
    )->flush($conn, $topic);
  }
}
