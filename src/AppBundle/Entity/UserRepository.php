<?php
namespace AppBundle\Entity;

class UserRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return User
   */
  public function findByID($id)
  {
    return $this->findOneBy([
      "id" => $id
    ]);
  }

  /**
   * Returns the user with the given username
   *
   * @param string $username
   *
   * @return User
   */
  public function findByUsername($username)
  {
    return $this->findOneBy([
      "username" => $username
    ]);
  }

  /**
   * Returns the user with the given email
   *
   * @param string $email
   *
   * @return User
   */
  public function findByEmail($email)
  {
    return $this->findOneBy([
      "email" => $email
    ]);
  }

  /**
   * @param string[] $usernames
   * @return User[]
   */
  public function findByUsernames(array $usernames)
  {
    return $this->createQueryBuilder("u")
      ->where("u.username IN (:usernames)")
      ->setParameter("usernames", $usernames)
      ->orderBy("u.username", "asc")
      ->getQuery()
      ->execute();
  }
}
