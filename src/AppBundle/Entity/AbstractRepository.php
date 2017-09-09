<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AbstractRepository extends EntityRepository
{
  /**
   * @param int $currentPage
   * @param int $limit
   * @return \Doctrine\ORM\Tools\Pagination\Paginator
   */
  public function findAllByPage($currentPage, $limit)
  {
    $query = $this->createQueryBuilder('e')
      ->orderBy('e.id', 'DESC')
      ->getQuery();

    return $this->paginate($query, $currentPage, $limit);
  }

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

  /**
   * @param object $entity
   */
  public function persistAndFlush($entity)
  {
    $em = $this->getEntityManager();
    $em->persist($entity);
    $em->flush($entity);
  }

  /**
   * Paginator Helper
   *
   * Pass through a query object, current page & limit
   * the offset is calculated from the page and limit
   * returns an `Paginator` instance, which you can call the following on:
   *
   *     $paginator->getIterator()->count() # Total fetched (ie: `5` posts)
   *     $paginator->count() # Count of ALL posts (ie: `20` posts)
   *     $paginator->getIterator() # ArrayIterator
   *
   * @param \Doctrine\ORM\Query $dql   DQL Query Object
   * @param integer            $page  Current page (defaults to 1)
   * @param integer            $limit The total number per page (defaults to 5)
   *
   * @return \Doctrine\ORM\Tools\Pagination\Paginator
   */
  public function paginate($dql, $page = 1, $limit = 5)
  {
    $paginator = new Paginator($dql);
    $paginator->getQuery()
      ->setFirstResult($limit * ($page - 1))
      ->setMaxResults($limit);

    return $paginator;
  }
}
