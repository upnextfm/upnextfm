<?php
namespace AppBundle\Entity;

class UserEventRepository extends AbstractRepository
{
  /**
   * Returns the event with the given id
   *
   * @param int $id
   *
   * @return UserEvent
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
   * @return UserEvent[]
   */
    public function findByUser(User $user, $limit, $offset = 0)
    {
        return $this->createQueryBuilder("ue")
        ->where("ue.user = :user")
        ->setParameter("user", $user)
        ->orderBy("ue.id", "desc")
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->getQuery()
        ->execute();
    }

    public function findAllByUser(User $user, $offset = 0)
    {
        return $this->createQueryBuilder("ue")
        ->where("ue.user = :user")
        ->setParameter("user", $user)
        ->orderBy("ue.id", "desc")
        ->setFirstResult($offset)
        ->getQuery()
        ->execute();
    }



}
