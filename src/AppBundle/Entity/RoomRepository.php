<?php
namespace AppBundle\Entity;

class RoomRepository extends AbstractRepository
{
  /**
   * Returns the room with the given id
   *
   * @param int $id
   *
   * @return Room
   */
  public function findByID($id)
  {
    return $this->findOneBy([
      "id" => $id
    ]);
  }

  /**
   * Returns the room with the given name
   *
   * @param string $name
   *
   * @return Room
   */
  public function findByName($name)
  {
    return $this->findOneBy([
      "name" => $name
    ]);
  }

  /**
   * @param int $limit
   * @return Room[]
   */
  public function findPublic($limit)
  {
    return $this->createQueryBuilder("r")
      ->where("r.isDeleted = 0")
      ->andWhere("r.isPrivate = 0")
      ->setMaxResults($limit)
      ->getQuery()
      ->execute();
  }

  /**
   * @param Room $room
   */
  public function save(Room $room)
  {
    $em = $this->getEntityManager();
    $em->persist($room);
    $em->flush();
  }
}
