<?php
namespace AppBundle\Entity;

class RoomRepository extends AbstractRepository
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
