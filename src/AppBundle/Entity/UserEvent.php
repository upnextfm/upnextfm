<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserEventRepository")
 * @ORM\Table(name="user_event")
 */
class UserEvent
{
  /**
   * @var int
   *
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string
   *
   * @ORM\Column(name="type", type="string", length=25, nullable=false)
   */
  protected $type;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   * })
   */
  protected $user;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="target_user_id", referencedColumnName="id", nullable=true)
   * })
   */
  protected $targetUser;

  /**
   * @var \AppBundle\Entity\Video
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Video", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="target_video_id", referencedColumnName="id", nullable=true)
   * })
   */
  protected $targetVideo;

  /**
   * @var \AppBundle\Entity\Room
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="target_room_id", referencedColumnName="id", nullable=true)
   * })
   */
  protected $targetRoom;

  /**
   * @var DateTime
   *
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
  protected $dateCreated;

  /**
   * Constructor
   *
   * @param string $type
   */
  public function __construct($type = null)
  {
    $this->type        = $type;
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
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param string $type
   * @return $this
   */
  public function setType($type)
  {
    $this->type = $type;
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
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return User
   */
  public function getTargetUser()
  {
    return $this->targetUser;
  }

  /**
   * @param User $targetUser
   * @return $this
   */
  public function setTargetUser($targetUser)
  {
    $this->targetUser = $targetUser;
    return $this;
  }

  /**
   * @return Video
   */
  public function getTargetVideo()
  {
    return $this->targetVideo;
  }

  /**
   * @param Video $targetVideo
   * @return $this
   */
  public function setTargetVideo($targetVideo)
  {
    $this->targetVideo = $targetVideo;
    return $this;
  }

  /**
   * @return Room
   */
  public function getTargetRoom()
  {
    return $this->targetRoom;
  }

  /**
   * @param Room $targetRoom
   * @return $this
   */
  public function setTargetRoom($targetRoom)
  {
    $this->targetRoom = $targetRoom;
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
  public function setDateCreated($dateCreated)
  {
    $this->dateCreated = $dateCreated;
    return $this;
  }
}
