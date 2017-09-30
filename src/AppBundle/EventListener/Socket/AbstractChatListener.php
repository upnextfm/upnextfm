<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\ChatLog;
use AppBundle\Entity\PrivateMessage;

class AbstractChatListener extends AbstractListener
{
  /**
   * @param string $message
   * @return string
   */
  protected function sanitizeMessage($message)
  {
    return trim(htmlspecialchars($message));
  }

  /**
   * @param ChatLog $message
   * @param string $type
   * @return array
   */
  protected function serializeMessage(ChatLog $message, $type = "message")
  {
    return [
      "type"    => $type,
      "id"      => $message->getId(),
      "date"    => $message->getDateCreated()->format("D M d Y H:i:s O"),
      "from"    => $message->getUser()->getUsername(),
      "message" => $message->getMessage()
    ];
  }

  /**
   * @param PrivateMessage $pm
   * @return array
   */
  protected function serializePrivateMessage(PrivateMessage $pm)
  {
    return [
      "id"      => $pm->getId(),
      "type"    => "message",
      "to"      => $pm->getToUser()->getUsername(),
      "from"    => $pm->getFromUser()->getUsername(),
      "date"    => $pm->getDateCreated()->format("D M d Y H:i:s O"),
      "message" => $pm->getMessage()
    ];
  }
}
