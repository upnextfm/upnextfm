<?php
namespace AppBundle\Entity;

class UploadRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return Upload
   */
    public function findByID($id)
    {
        return $this->findOneBy([
        "id" => $id
        ]);
    }
}
