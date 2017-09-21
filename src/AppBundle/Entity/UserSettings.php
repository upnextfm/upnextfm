<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_settings")
 * @ORM\HasLifecycleCallbacks()
 */
class UserSettings
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
   * @var \AppBundle\Entity\User
   *
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"}, inversedBy="settings")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   */
  protected $user;

  /**
   * @var boolean
   *
   * @ORM\Column(name="show_notices", type="boolean", nullable=false)
   */
  protected $showNotices = true;

  /**
   * @var string
   *
   * @ORM\Column(name="text_color", type="string", length=7, nullable=false)
   */
  protected $textColor = "#FFFFFF";

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
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param User $user
   * @return UserSettings
   */
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isShowNotices()
  {
    return $this->showNotices;
  }

  /**
   * @return bool
   */
  public function getShowNotices()
  {
    return $this->showNotices;
  }

  /**
   * @param boolean $showNotices
   * @return UserSettings
   */
  public function setShowNotices($showNotices)
  {
    $this->showNotices = $showNotices;
    return $this;
  }

  /**
   * @return string
   */
  public function getTextColor()
  {
    return $this->textColor;
  }

  /**
   * @param string $textColor
   * @return UserSettings
   */
  public function setTextColor($textColor)
  {
    $this->textColor = $textColor;
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
  public function setDateUpdated(DateTime $dateUpdated)
  {
    $this->dateUpdated = $dateUpdated;
    return $this;
  }
}
