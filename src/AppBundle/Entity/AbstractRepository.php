<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AbstractRepository extends EntityRepository
{
  /**
   * Returns the total number of entities in the table
   *
   * @return int
   */
  public function countAll()
  {
    return $this->createQueryBuilder("e")
      ->select("COUNT(e)")
      ->getQuery()
      ->getSingleScalarResult();
  }
}
