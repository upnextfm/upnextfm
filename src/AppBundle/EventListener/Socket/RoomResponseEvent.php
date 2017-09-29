<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\Room;
use Symfony\Component\EventDispatcher\Event;

class RoomResponseEvent extends Event
{
  /**
   * @var Room
   */
  protected $room;

  /**
   * @var string
   */
  protected $action;

  /**
   * @var array
   */
  protected $args = [];

  /**
   * Constructor
   *
   * @param Room $room
   * @param string $action
   * @param array $args
   */
  public function __construct(Room $room, $action, array $args = [])
  {
    $this->room   = $room;
    $this->action = $action;
    $this->args   = $args;
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
   * @return RoomResponseEvent
   */
  public function setRoom($room)
  {
    $this->room = $room;
    return $this;
  }

  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * @param string $action
   * @return RoomResponseEvent
   */
  public function setAction($action)
  {
    $this->action = $action;
    return $this;
  }

  /**
   * @return array
   */
  public function getArgs()
  {
    return $this->args;
  }

  /**
   * @param array $args
   * @return RoomResponseEvent
   */
  public function setArgs($args)
  {
    $this->args = $args;
    return $this;
  }
}
