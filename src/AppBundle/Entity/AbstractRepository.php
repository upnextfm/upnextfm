<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class AbstractRepository extends EntityRepository
{
  /**
   * @param int $id
   * @return null|object
   */
  public function findByID($id)
  {
    return $this->findOneBy([
      "id" => $id
    ]);
  }

  /**
   * @param int $currentPage
   * @param int $limit
   * @param array $filters
   * @return \Doctrine\ORM\Tools\Pagination\Paginator
   */
  public function findAllByPage($currentPage, $limit, $filters = [])
  {
    $query = $this->createQueryBuilder('e')
      ->orderBy('e.id', 'DESC');

    if ($filters) {
      foreach($filters as $column => $value) {
        if (preg_match('/[^a-z]/i', $column)) {
          throw new \InvalidArgumentException(sprintf(
            'Invalid filter column "%s". Contains special characters.',
            $column
          ));
        }
        $query->andWhere("e.${column} = :${column}");
        $query->setParameter($column, $value);
      }
    }

    return $this->paginate($query->getQuery(), $currentPage, $limit);
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

  /**
   * @param object $target
   * @param array $values
   * @return object
   */
  public function hydrateFromArray($target, array $values)
  {
    $metaData  = $this->getClassMetadata();
    return $this->hydrateFromMetaDataArray($metaData, $target, $values);
  }

  /**
   * @param ClassMetadata $metaData
   * @param object $target
   * @param array $values
   * @return object
   * @throws \Doctrine\ORM\Mapping\MappingException
   * @throws \Doctrine\ORM\ORMException
   */
  private function hydrateFromMetaDataArray(ClassMetadata $metaData, $target, array $values)
  {
    $accessor  = PropertyAccess::createPropertyAccessor();
    $em        = $this->getEntityManager();

    foreach($values as $key => $value) {
      if ($key === 'id') {
        continue;
      }

      if ($metaData->hasField($key)) {
        if ($metaData->getTypeOfField($key) === 'datetime') {
          $value = new \DateTime($value);
        }
        $accessor->setValue($target, $key, $value);
      } else if ($metaData->hasAssociation($key)) {
        $assoc       = $metaData->getAssociationMapping($key);
        $ref         = $em->getReference($assoc['targetEntity'], $value);
        $repo        = $em->getRepository($assoc['targetEntity']);
        $refMetaData = $repo->getClassMetadata();
        $this->hydrateFromMetaDataArray($refMetaData, $ref, $value);
        $accessor->setValue($target, $key, $ref);
      }
    }

    return $target;
  }
}
