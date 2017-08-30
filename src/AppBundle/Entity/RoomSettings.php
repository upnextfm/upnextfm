<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="room_settings")
 * @ORM\HasLifecycleCallbacks()
 */
class RoomSettings
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
   * @var \AppBundle\Entity\Room
   *
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\Room", cascade={"persist"}, inversedBy="settings")
   * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
   */
  protected $room;

  /**
   * @var boolean
   *
   * @ORM\Column(name="is_public", type="boolean", nullable=false)
   */
  protected $isPublic = true;

  /**
   * @var string
   *
   * @ORM\Column(name="thumb_color", type="string", length=6, nullable=false)
   */
  protected $thumbColor = "000000";

  /**
   * @var string
   *
   * @ORM\Column(name="thumb_sm", type="string", nullable=false)
   */
  protected $thumbSm;

  /**
   * @var string
   *
   * @ORM\Column(name="thumb_md", type="string", nullable=false)
   */
  protected $thumbMd;

  /**
   * @var string
   *
   * @ORM\Column(name="thumb_lg", type="string", nullable=false)
   */
  protected $thumbLg;

  /**
   * @var string
   *
   * @ORM\Column(name="join_message", type="text", length=2000, nullable=true)
   */
  protected $joinMessage = "";

  /**
   * @var DateTime
   *
   * @ORM\Column(name="date_updated", type="datetime", nullable=false)
   */
  protected $dateUpdated;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->dateUpdated = new DateTime();
  }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function updateDateUpdated() {
    $this->setDateUpdated(new DateTime());
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param Room $room
   * @return $this
   */
  public function setRoom($room)
  {
    $this->room = $room;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isPublic()
  {
    return $this->isPublic;
  }

  /**
   * @param boolean $isPublic
   * @return $this
   */
  public function setIsPublic($isPublic)
  {
    $this->isPublic = $isPublic;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbColor()
  {
    return $this->thumbColor;
  }

  /**
   * @param string $thumbColor
   * @return $this
   */
  public function setThumbColor($thumbColor)
  {
    $this->thumbColor = $thumbColor;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbSm()
  {
    return $this->thumbSm;
  }

  /**
   * @param string $thumbSm
   * @return $this
   */
  public function setThumbSm($thumbSm)
  {
    $this->thumbSm = $thumbSm;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbMd()
  {
    return $this->thumbMd;
  }

  /**
   * @param string $thumbMd
   * @return $this
   */
  public function setThumbMd($thumbMd)
  {
    $this->thumbMd = $thumbMd;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbLg()
  {
    return $this->thumbLg;
  }

  /**
   * @param string $thumbLg
   * @return $this
   */
  public function setThumbLg($thumbLg)
  {
    $this->thumbLg = $thumbLg;
    return $this;
  }

  /**
   * @return string
   */
  public function getJoinMessage()
  {
    return $this->joinMessage;
  }

  /**
   * @param string $joinMessage
   * @return $this
   */
  public function setJoinMessage($joinMessage)
  {
    $this->joinMessage = $joinMessage;
    return $this;
  }

  /**
   * @return DateTime
   */
  public function getDateUpdated()
  {
    return $this->dateUpdated;
  }

  /**
   * @param DateTime $dateUpdated
   * @return $this
   */
  public function setDateUpdated($dateUpdated)
  {
    $this->dateUpdated = $dateUpdated;
    return $this;
  }
}
