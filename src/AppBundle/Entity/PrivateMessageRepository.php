<?php
namespace AppBundle\Entity;

class PrivateMessageRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return PrivateMessage
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
   * @return PrivateMessage[]
   */
    public function fetchToUser(User $user, $limit, $offset = 0)
    {
        return $this->createQueryBuilder("pm")
        ->where("pm.toUser = :user")
        ->setParameter("user", $user)
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->orderBy("id", "desc")
        ->getQuery()
        ->execute();
    }

  /**
   * @param User $user
   * @param int $limit
   * @param int $offset
   * @return PrivateMessage[]
   */
    public function fetchFromUser(User $user, $limit, $offset = 0)
    {
        return $this->createQueryBuilder("pm")
        ->where("pm.fromUser = :user")
        ->setParameter("user", $user)
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->orderBy("id", "desc")
        ->getQuery()
        ->execute();
    }

  /**
   * @param User $toUser
   * @param User $fromUser
   * @param int $limit
   * @param int $offset
   * @return PrivateMessage[]
   */
    public function fetchConversation(User $toUser, User $fromUser, $limit, $offset = 0)
    {
        return $this->createQueryBuilder("pm")
        ->where("(pm.toUser = :toUser AND pm.fromUser = :fromUser)")
        ->orWhere("(pm.toUser = :fromUser ANd pm.fromUser = :toUser)")
        ->setParameter("toUser", $toUser)
        ->setParameter("fromUser", $fromUser)
        ->setFirstResult($offset)
        ->setMaxResults($limit)
        ->orderBy("pm.id", "desc")
        ->getQuery()
        ->execute();
    }
}
