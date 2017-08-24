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
    $this->redis->pipeline(function($pipe) use($room, $user) {
      /** @var Redis $pipe */
      $pipe->sadd($this->keyRoomUsers($room), $user->getUsername());
      $pipe->sadd($this->keyUserRooms($user), $room->getName());
    });

  }

  /**
   * Removes a user from the given room
   *
   * @param Room $room
   * @param UserInterface $user
   */
  public function removeUser(Room $room, UserInterface $user)
  {
    $this->redis->pipeline(function($pipe) use($room, $user) {
      /** @var Redis $pipe */
      $pipe->srem($this->keyRoomUsers($room), $user->getUsername());
      $pipe->srem($this->keyUserRooms($user), $room->getName());
    });
  }

  /**
   * Returns the usernames of the users in the given room
   *
   * @param Room $room
   * @return array
   */
  public function getRoomUsers(Room $room)
  {
    return $this->redis->smembers($this->keyRoomUsers($room));
  }

  /**
   * Returns the names of the rooms the user is in
   *
   * @param UserInterface $user
   * @return array
   */
  public function getUserRooms(UserInterface $user)
  {
    return $this->redis->smembers($this->keyUserRooms($user));
  }

  /**
   * Returns the number of users in the given room
   *
   * @param Room $room
   * @return int
   */
  public function getRoomUserCount(Room $room)
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

  /**
   * @param UserInterface $user
   * @return string
   */
  private function keyUserRooms(UserInterface $user)
  {
    return sprintf("users:%s:rooms", $user->getUsername());
  }
}
