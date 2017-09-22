<?php
namespace AppBundle\Playlist;

use AppBundle\Entity\Room;
use AppBundle\Entity\VideoLog;
use AppBundle\Entity\VideoLogRepository;

class RngMod
{
  /**
   * @var VideoLogRepository
   */
    protected $videoLogRepository;

  /**
   * Constructor
   *
   * @param VideoLogRepository $videoLogRepository
   */
    public function __construct(VideoLogRepository $videoLogRepository)
    {
        $this->videoLogRepository = $videoLogRepository;
    }

  /**
   * @param Room $room
   * @param int $limit
   * @return VideoLog[]
   */
    public function findByRoom(Room $room, $limit = 3)
    {
        return $this->videoLogRepository->createQueryBuilder("vl")
        ->addSelect('RAND() as HIDDEN rand')
        ->where("vl.room = :room")
        ->setParameter("room", $room)
        ->setMaxResults($limit)
        ->orderBy('rand')
        ->getQuery()
        ->execute();
    }
}
