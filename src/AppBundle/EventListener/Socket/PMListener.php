<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\PrivateMessage;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class PMListener extends AbstractChatListener
{
  /**
   * @param UserInterface|User $user
   * @param string $username
   */
  public function onLoad(UserInterface $user, $username)
  {
    /** @var PrivateMessage $pm */
    $toUser = $this->em->getRepository("AppBundle:User")
      ->findByUsername($username);
    if (!($toUser instanceof UserInterface)) {
      return;
    }

    $conversation = [];
    $repo = $this->em->getRepository("AppBundle:PrivateMessage");
    foreach ($repo->fetchConversation($user, $toUser, 50) as $row) {
      $conversation[] = $this->serializePrivateMessage($row);
    }

    $event = new PMResponseEvent($user, "pms:pmsLoad", [
      $toUser->getUsername(),
      array_reverse($conversation)
    ]);
    $this->eventDispatcher->dispatch(SocketEvents::PM_RESPONSE, $event);
  }

  /**
   * @param UserInterface $user
   * @param string $to
   * @param string $message
   * @return bool|void
   */
  public function onSend(UserInterface $user, $to, $message)
  {
    if (empty($message)) {
      return $this->logger->error("Missing 'message' parameter.");
    }
    if (empty($to)) {
      return $this->logger->error("Missing 'to' parameter.");
    }
    $message = $this->sanitizeMessage($message);
    if (empty($message)) {
      $this->logger->error("Empty 'message' parameter.");
      return true;
    }

    /** @var User $toUser */
    /** @var PrivateMessage $pm */
    $toUser = $this->em->getRepository("AppBundle:User")
      ->findByUsername($to);
    if (!($toUser instanceof UserInterface)) {
      return false;
    }

    $pm = (new PrivateMessage())
      ->setFromUser($user)
      ->setToUser($toUser)
      ->setMessage($message);
    $pm = $this->em->merge($pm);
    $this->em->flush();

    $message = $this->serializePrivateMessage($pm);
    $event = new PMResponseEvent($toUser, "pms:pmsReceive", [
      $message
    ]);
    $this->eventDispatcher->dispatch(SocketEvents::PM_RESPONSE, $event);

    $event = new PMResponseEvent($user, "pms:pmsSent", [
      $message
    ]);
    $this->eventDispatcher->dispatch(SocketEvents::PM_RESPONSE, $event);
  }
}
