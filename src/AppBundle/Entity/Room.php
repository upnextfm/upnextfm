<?php
namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoomRepository")
 * @ORM\Table(name="room")
 */
class Room
{
  /**
   * @var int
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string
   * @Groups({"elastica"})
   * @ORM\Column(name="name", type="string", length=25, nullable=false)
   */
  protected $name = "";

  /**
   * @var string
   * @Groups({"elastica"})
   * @ORM\Column(name="display_name", type="string", length=50, nullable=false)
   */
  protected $displayName = "";

  /**
   * @var string
   * @ORM\Column(name="description", type="text", nullable=true)
   */
  protected $description = "";

  /**
   * @var boolean
   * @ORM\Column(name="is_private", type="boolean", nullable=false)
   */
  protected $isPrivate = false;

  /**
   * @var boolean
   * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
   */
  protected $isDeleted = false;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
   * })
   */
  protected $createdByUser;

  /**
   * @var DateTime
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
  protected $dateCreated;

  /**
   * @var RoomSettings
   *
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\RoomSettings", mappedBy="room", fetch="EAGER", cascade={"persist"})
   */
  protected $settings;

  /**
   * Constructor
   *
   * @param string $name
   * @param null $createdByUser
   */
  public function __construct($name = "", $createdByUser = null)
  {
    $this->name          = $name;
    $this->createdByUser = $createdByUser;
    $this->dateCreated   = new DateTime();
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
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param string $name
   * @return $this
   */
  public function setName($name)
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   * @return $this
   */
  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isPrivate()
  {
    return $this->isPrivate;
  }

  /**
   * @param boolean $isPrivate
   * @return $this
   */
  public function setIsPrivate($isPrivate)
  {
    $this->isPrivate = $isPrivate;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isDeleted()
  {
    return $this->isDeleted;
  }

  /**
   * @param boolean $isDeleted
   * @return $this
   */
  public function setIsDeleted($isDeleted)
  {
    $this->isDeleted = $isDeleted;
    return $this;
  }

  /**
   * @return User
   */
  public function getCreatedByUser()
  {
    return $this->createdByUser;
  }

  /**
   * @param User $createdByUser
   * @return $this
   */
  public function setCreatedByUser($createdByUser)
  {
    $this->createdByUser = $createdByUser;
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

  /**
   * @return RoomSettings
   */
  public function getSettings()
  {
    return $this->settings;
  }

  /**
   * @param RoomSettings $settings
   * @return $this
   */
  public function setSettings($settings)
  {
    $this->settings = $settings;
    return $this;
  }
}
