<?php
namespace AdminBundle\Handler;

use AppBundle\Entity\AbstractRepository;

class RoomHandler extends AbstractHandler
{

  /**
   * @return AbstractRepository
   */
  public function getRepository()
  {
    return $this->doctrine->getRepository("AppBundle:Room");
  }

  /**
   * @param int $id
   * @return null|object
   */
  public function getEntityByID($id)
  {
    return $this->getRepository()->findByID($id);
  }

  /**
   * @return string
   */
  public function getFilterColumn()
  {
    return "name";
  }

  /**
   * @return array
   */
  public function getTableColumns()
  {
    return [
      "id"          => "ID",
      "name"        => "Name",
      "displayName" => "Display Name",
      "isPrivate"   => "Private",
      "isDeleted"   => "Deleted",
      "dateCreated" => "Date Created"
    ];
  }

  /**
   * @return array
   */
  public function getHydrateColumns()
  {
    return ["name", "displayName", "isPrivate", "isDeleted", "description"];
  }
}
