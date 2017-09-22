<?php

namespace AppBundle\Entity;

class VoteRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return Vote
   */
    public function findByID($id)
    {
        return $this->findOneBy([
        "id" => $id
        ]);
    }
}
