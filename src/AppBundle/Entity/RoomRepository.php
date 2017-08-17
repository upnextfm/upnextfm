<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
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
}
