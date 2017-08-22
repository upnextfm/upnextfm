<?php
namespace AppBundle\Storage;

use AppBundle\Entity\Room;
use Predis\Client as Redis;
use Symfony\Component\Security\Core\User\UserInterface;

class RoomStorage
{
  /**
   * @var Redis
   */
  protected $redis;

  /**
   * Constructor
   *
   * @param Redis $redis
   */
  public function __construct(Redis $redis)
  {
    $this->redis = $redis;
  }

  /**
   * Adds a user to the given room
   *
   * @param Room $room
   * @param UserInterface $user
   */
  public function addUser(Room $room, UserInterface $user)
  {
    $this->redis->sadd($this->keyRoomUsers($room), $user->getUsername());
  }

  /**
   * Removes a user from the given room
   *
   * @param Room $room
   * @param UserInterface $user
   */
  public function removeUser(Room $room, UserInterface $user)
  {
    $this->redis->srem($this->keyRoomUsers($room), $user->getUsername());
  }

  /**
   * Returns the usernames of the users in the given room
   *
   * @param Room $room
   * @return array
   */
  public function getUsers(Room $room)
  {
    return $this->redis->smembers($this->keyRoomUsers($room));
  }

  /**
   * Returns the number of users in the given room
   *
   * @param Room $room
   * @return int
   */
  public function getUserCount(Room $room)
  {
    return (int)$this->redis->scard($this->keyRoomUsers($room));
  }

  /**
   * @param Room $room
   * @return string
   */
  private function keyRoomUsers(Room $room)
  {
    return sprintf("room:%s:users", $room->getName());
  }
}
