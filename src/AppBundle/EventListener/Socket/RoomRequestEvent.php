<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class RoomRequestEvent extends Event
{
  /**
   * @var UserInterface|User
   */
  protected $user;

  /**
   * @var Room
   */
  protected $room;

  /**
   * @var array
   */
  protected $payload = [];

  /**
   * Constructor
   *
   * @param Room $room
   * @param UserInterface|User $user
   * @param array $payload
   */
  public function __construct(Room $room, UserInterface $user, array $payload)
  {
    $this->room    = $room;
    $this->user    = $user;
    $this->payload = $payload;
  }

  /**
   * @return array
   */
  public function getPayload()
  {
    return $this->payload;
  }

  /**
   * @param array $payload
   * @return $this
   */
  public function setPayload(array $payload)
  {
    $this->payload = $payload;
    return $this;
  }

  /**
   * @return User|UserInterface
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User|UserInterface $user
   * @return RoomRequestEvent
   */
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return Room
   */
  public function getRoom()
  {
    return $this->room;
  }

  /**
   * @param Room $room
   * @return RoomRequestEvent
   */
  public function setRoom($room)
  {
    $this->room = $room;
    return $this;
  }
}
