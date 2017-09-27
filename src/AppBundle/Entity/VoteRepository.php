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
   * @param User $user
   * @param Video $video
   * @return boolean
   */
  public function hasVoted(User $user, Video $video)
  {
    $vote = $this->findOneBy([
      "user" => $user,
      "video" => $video
    ]);

    if ($vote) {
      return true;
    } else {
      return false;
    }
  }
}
