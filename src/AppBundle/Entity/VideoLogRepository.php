<?php
namespace AppBundle\Entity;

class VideoLogRepository extends AbstractRepository
{
  /**
   * Returns the room with the given name
   *
   * @param int $id
   *
   * @return VideoLog
   */
    public function findByID($id)
    {
        return $this->findOneBy([
        "id" => $id
        ]);
    }

  /**
   * Returns the most recent logs
   *
   * @param int $limit
   * @param int $offset
   * @return VideoLog[]
   */
    public function findRecent($limit, $offset = 0)
    {
        return $this->createQueryBuilder("v")
        ->join("AppBundle:Room", "r", "with", "v.room = r")
        ->where("r.isPrivate = 0")
        ->andWhere("r.isDeleted = 0")
        ->orderBy("v.id", "desc")
        ->setMaxResults($limit)
        ->setFirstResult($offset)
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
