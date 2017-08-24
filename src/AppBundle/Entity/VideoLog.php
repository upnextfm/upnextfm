<?php
namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VideoLogRepository")
 * @ORM\Table(name="video_log")
 */
class VideoLog
{
  /**
   * @var int
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var \AppBundle\Entity\Video
   *
   * @Groups({"elastica"})
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Video", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="video_id", referencedColumnName="id")
   * })
   */
  protected $video;

  /**
   * @var \AppBundle\Entity\Room
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="room_id", referencedColumnName="id")
   * })
   */
  protected $room;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   * })
   */
  protected $user;

  /**
   * @var DateTime
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
  protected $dateCreated;

  /**
   * Constructor
   *
   * @param Video $video
   * @param Room $room
   * @param User $user
   */
  public function __construct(Video $video = null, Room $room = null, User $user = null)
  {
    $this->video       = $video;
    $this->room        = $room;
    $this->user        = $user;
    $this->dateCreated = new DateTime();
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return Video
   */
  public function getVideo()
  {
    return $this->video;
  }

  /**
   * @param Video $video
   * @return $this
   */
  public function setVideo(Video $video)
  {
    $this->video = $video;
    return $this;
  }

  /**
   * @return Room
   */
  public function getRoom()
  {
    return $this->room;
  }

  /**
   * @param Room $room
   * @return $this
   */
  public function setRoom(Room $room)
  {
    $this->room = $room;
    return $this;
  }

  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return $this
   */
  public function setUser(User $user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getDateCreated()
  {
    return $this->dateCreated;
  }

  /**
   * @param DateTime $dateCreated
   * @return $this
   */
  public function setDateCreated(DateTime $dateCreated)
  {
    $this->dateCreated = $dateCreated;
    return $this;
  }
}
