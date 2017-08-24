<?php
namespace AppBundle\Entity;

class VideoRepository extends AbstractRepository
{
  /**
   * @param int $id
   * @return Video
   */
  public function findByID($id)
  {
    return $this->findOneBy([
      "id" => $id
    ]);
  }

  /**
   * Returns the video with the given codename and provider
   *
   * @param string $codename
   * @param string $provider
   * @return Video
   */
  public function findByCodename($codename, $provider)
  {
    return $this->findOneBy([
      "codename" => $codename,
      "provider" => $provider
    ]);
  }
}
