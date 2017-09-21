<?php
namespace AppBundle\Entity;

class FavoriteRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return Favorite
   */
  public function findByID($id)
  {
    return $this->findOneBy([
      "id" => $id
    ]);
  }

  /**
   * @param User $user
   * @param int $limit
   * @param int $offset
   * @return Favorite[]
   */
  public function findByUser(User $user, $limit, $offset = 0)
  {
    return $this->createQueryBuilder("f")
      ->where("f.user = :user")
      ->setParameter("user", $user)
      ->setFirstResult($offset)
      ->setMaxResults($limit)
      ->orderBy("f.id", "desc")
      ->getQuery()
      ->execute();
  }

  /**
   * Returns the total number of entities in the table for a user
   *
   * @param User $user
   * @return int
   */
  public function countByUser(User $user)
  {
    return $this->createQueryBuilder("f")
      ->where("f.user = :user")
      ->setParameter("user", $user)
      ->select("COUNT(f)")
      ->getQuery()
      ->getSingleScalarResult();
  }
}
