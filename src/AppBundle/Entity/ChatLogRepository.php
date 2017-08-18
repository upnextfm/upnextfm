<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChatLogRepository extends EntityRepository
{
  /**
   * Returns the most recent logs for the given room
   *
   * @param Room $room
   * @param int $limit
   * @return ChatLog[]
   */
  public function findRecent(Room $room, $limit)
  {
    return $this->createQueryBuilder("c")
      ->where("c.room = :room")
      ->setParameter("room", $room)
      ->orderBy("c.id", "desc")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }
}
