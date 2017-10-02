<?php
namespace AppBundle\EventListener\Socket;

use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\ChatLog;
use AppBundle\Entity\Room;
use AppBundle\Entity\RoomSettings;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSettings;

/**
 * Handles client side events related to the room topic.
 */
class RoomListener extends AbstractChatListener
{
  /**
   * @param UserInterface $user
   * @param Room $room
   * @param string $message
   */
  public function onSend(UserInterface $user, Room $room, $message)
  {
    $message = $this->sanitizeMessage($message);
    if (!empty($message)) {
      $event = new RoomResponseEvent($room, RoomActions::MESSAGE, [
        $this->serializeMessage($this->getChatLog($room, $user, $message))
      ]);
      $this->eventDispatcher->dispatch(SocketEvents::ROOM_RESPONSE, $event);
    }
  }

  /**
   * @param UserInterface $user
   * @param Room $room
   * @param string $message
   */
  public function onMe(UserInterface $user, Room $room, $message)
  {
    $message = $this->sanitizeMessage($message);
    if (!empty($message)) {
      $event = new RoomResponseEvent($room, RoomActions::MESSAGE, [
        $this->serializeMessage($this->getChatLog($room, $user, $message))
      ]);
      $this->eventDispatcher->dispatch(SocketEvents::ROOM_RESPONSE, $event);
    }
  }

  /**
   * @param UserInterface $user
   * @param Room $room
   * @param array $settings
   * @param string $type
   */
  public function onSaveSettings(UserInterface $user, Room $room, array $settings, $type)
  {
    switch($type) {
      case "user":
        $this->saveUserSettings($user, $settings);
        break;
      case "room":
        $this->saveRoomSettings($room, $settings);
        break;
    }
  }

  /**
   * @param UserInterface|User $user
   * @param array $settings
   */
  private function saveUserSettings(UserInterface $user, array $settings)
  {
    $settings["showNotices"] = isset($settings["showNotices"])
      ? $settings["showNotices"]
      : true;
    $settings["textColor"] = isset($settings["textColor"])
      ? $settings["textColor"]
      : "#FFFFFF";

    $userSettings = $user->getSettings();
    if (!$userSettings) {
      $userSettings = new UserSettings();
      $userSettings->setUser($user);
      $user->setSettings($userSettings);
    }
    $userSettings->setShowNotices($settings["showNotices"]);
    $userSettings->setTextColor($settings["textColor"]);
    $this->em->flush();
  }

  /**
   * @param Room $room
   * @param array $settings
   */
  private function saveRoomSettings(Room $room, array $settings)
  {
    $settings["joinMessage"] = isset($settings["joinMessage"])
      ? $settings["joinMessage"]
      : "";

    $roomSettings = $room->getSettings();
    if (!$roomSettings) {
      $roomSettings = new RoomSettings();
      $roomSettings->setRoom($room);
      $room->setSettings($roomSettings);
    }

    $roomSettings->setJoinMessage($settings["joinMessage"]);
    $this->em->flush();
  }

  /**
   * @param Room $room
   * @param UserInterface|User $user
   * @param string $message
   * @return ChatLog
   */
  private function getChatLog(Room $room, UserInterface $user, $message)
  {
    $chatLog = new ChatLog($room, $user, $message);
    $chatLog = $this->em->merge($chatLog);
    $this->em->flush();

    return $chatLog;
  }
}
