<?php
namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository
{
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
