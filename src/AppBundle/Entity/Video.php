<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VideoRepository")
 * @ORM\Table(name="video")
 */
class Video
{
  const PROVIDER_YOUTUBE = "youtube";

  /**
   * @var int
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var string
   * @ORM\Column(name="title", type="string", length=100, nullable=false)
   */
  protected $title;

  /**
   * @var string
   * @ORM\Column(name="codename", type="string", length=50, nullable=false)
   */
  protected $codename;

  /**
   * @var string
   * @ORM\Column(name="permalink", type="string", nullable=false)
   */
  protected $permalink;

  /**
   * @var string
   * @ORM\Column(name="provider", type="string", length=50, nullable=false)
   */
  protected $provider = "youtube";

  /**
   * @var int
   * @ORM\Column(name="seconds", type="integer", nullable=false)
   */
  protected $seconds = 0;

  /**
   * @var int
   * @ORM\Column(name="num_plays", type="integer", nullable=false)
   */
  protected $numPlays = 0;

  /**
   * @var string
   * @ORM\Column(name="thumb_small", type="string", nullable=false)
   */
  protected $thumbSmall;

  /**
   * @var string
   * @ORM\Column(name="thumb_medium", type="string", nullable=false)
   */
  protected $thumbMedium;

  /**
   * @var string
   * @ORM\Column(name="thumb_large", type="string", nullable=false)
   */
  protected $thumbLarge;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id")
   * })
   */
  protected $createdByUser;

  /**
   * @var \AppBundle\Entity\Room
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="created_in_room_id", referencedColumnName="id")
   * })
   */
  protected $createdInRoom;

  /**
   * @var DateTime
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
  protected $dateCreated;

  /**
   * @var DateTime
   * @ORM\Column(name="date_last_played", type="datetime", nullable=false)
   */
  protected $dateLastPlayed;

  /**
   * @param string $provider
   * @return bool
   */
  public static function isValidProvider($provider)
  {
    if (!in_array($provider, [self::PROVIDER_YOUTUBE])) {
      return false;
    }

    return true;
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->dateCreated    = new DateTime();
    $this->dateLastPlayed = new DateTime();
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
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return $this
   */
  public function setTitle($title)
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getCodename()
  {
    return $this->codename;
  }

  /**
   * @param string $codename
   * @return $this
   */
  public function setCodename($codename)
  {
    $this->codename = $codename;
    return $this;
  }

  /**
   * @return string
   */
  public function getPermalink()
  {
    return $this->permalink;
  }

  /**
   * @param string $permalink
   * @return $this
   */
  public function setPermalink($permalink)
  {
    $this->permalink = $permalink;
    return $this;
  }

  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }

  /**
   * @param string $provider
   * @return $this
   */
  public function setProvider($provider)
  {
    if (!self::isValidProvider($provider)) {
      throw new \InvalidArgumentException(sprintf(
        "Provider invalid '%s'.",
        $provider
      ));
    }
    $this->provider = $provider;

    return $this;
  }

  /**
   * @return int
   */
  public function getSeconds()
  {
    return $this->seconds;
  }

  /**
   * @param int $seconds
   * @return $this
   */
  public function setSeconds($seconds)
  {
    $this->seconds = $seconds;
    return $this;
  }

  /**
   * @return int
   */
  public function getNumPlays()
  {
    return $this->numPlays;
  }

  /**
   * @param int $numPlays
   * @return $this
   */
  public function setNumPlays($numPlays)
  {
    $this->numPlays = $numPlays;
    return $this;
  }

  /**
   * @return $this
   */
  public function incrNumPlays()
  {
    $this->numPlays++;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbSmall()
  {
    return $this->thumbSmall;
  }

  /**
   * @param string $thumbSmall
   * @return $this
   */
  public function setThumbSmall($thumbSmall)
  {
    $this->thumbSmall = $thumbSmall;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbMedium()
  {
    return $this->thumbMedium;
  }

  /**
   * @param string $thumbMedium
   * @return $this
   */
  public function setThumbMedium($thumbMedium)
  {
    $this->thumbMedium = $thumbMedium;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbLarge()
  {
    return $this->thumbLarge;
  }

  /**
   * @param string $thumbLarge
   * @return $this
   */
  public function setThumbLarge($thumbLarge)
  {
    $this->thumbLarge = $thumbLarge;
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
  public function setCreatedByUser(User $createdByUser)
  {
    $this->createdByUser = $createdByUser;
    return $this;
  }

  /**
   * @return Room
   */
  public function getCreatedInRoom()
  {
    return $this->createdInRoom;
  }

  /**
   * @param Room $createdInRoom
   * @return $this
   */
  public function setCreatedInRoom(Room $createdInRoom)
  {
    $this->createdInRoom = $createdInRoom;
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

  /**
   * @return DateTime
   */
  public function getDateLastPlayed()
  {
    return $this->dateLastPlayed;
  }

  /**
   * @param DateTime $dateLastPlayed
   * @return $this
   */
  public function setDateLastPlayed(DateTime $dateLastPlayed)
  {
    $this->dateLastPlayed = $dateLastPlayed;
    return $this;
  }
}
