<?php
namespace AppBundle\EventListener\Event;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;

class CreatedRoomEvent extends UserEvent
{
  /**
   * Constructor
   *
   * @param User $user
   * @param Room $room
   */
    public function __construct(User $user, Room $room)
    {
        $this->setUser($user);
        $this->setRoom($room);
    }
}
