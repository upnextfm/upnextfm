<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class ChatLogRepository extends AbstractRepository
{
  /**
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

  /**
   * @param UserInterface $user
   * @param $limit
   * @return ChatLog[]
   */
  public function findFirstByUser(UserInterface $user, $limit)
  {
    return $this->createQueryBuilder("c")
      ->where("c.user = :user")
      ->setParameter("user", $user)
      ->orderBy("c.id", "asc")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }
}
