<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VideoLogRepository extends EntityRepository
{
  /**
   * Returns the most recent logs
   *
   * @param int $limit
   * @return VideoLog[]
   */
  public function findRecent($limit)
  {
    return $this->createQueryBuilder("v")
      ->orderBy("v.id", "desc")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }

  /**
   * Returns the most recent logs for the given room
   *
   * @param Room $room
   * @param int $limit
   * @return VideoLog[]
   */
  public function findRecentByRoom(Room $room, $limit)
  {
    return $this->createQueryBuilder("v")
      ->where("v.room = :room")
      ->setParameter("room", $room)
      ->orderBy("v.id", "desc")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }

  /**
   * Returns the most recent logs for the given user
   *
   * @param User $user
   * @param int $limit
   * @return VideoLog[]
   */
  public function findRecentByUser(User $user, $limit)
  {
    return $this->createQueryBuilder("v")
      ->where("v.user = :user")
      ->setParameter("user", $user)
      ->orderBy("v.id", "desc")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }
}