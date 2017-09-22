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


		/**
		 * @return Vote[]
		 */
    public function findMostUpvotedVideos()
    {
        return $this->createQueryBuilder("vt")
        		->join("AppBundle:Video", "vd", "with", "vt.video = vd")
        		->orderBy("vt.value", "asc")
        		->setMaxResults("25")
        		->getQuery()
        		->execute();
    }
}
