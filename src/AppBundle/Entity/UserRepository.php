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
   * @return User[]
   */
  public function findFoundingMembers()
  {
    return $this->createQueryBuilder("u")
      ->where("u.id < 37")
      ->orderBy("u.username", "asc")
      ->getQuery()
      ->execute();
  }
}
