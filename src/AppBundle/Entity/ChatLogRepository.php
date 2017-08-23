<?php
namespace AppBundle\Entity;

class ChatLogRepository extends AbstractRepository
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
