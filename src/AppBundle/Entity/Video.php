<?php
namespace AppBundle\Entity;

use AppBundle\Playlist\Providers;
use Symfony\Component\Serializer\Annotation\Groups;
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
   * @Groups({"elastica"})
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
   *
   * @ORM\Column(name="thumb_color", type="string", length=6, nullable=false)
   */
  protected $thumbColor = "000000";

  /**
   * @var string
   * @ORM\Column(name="thumb_sm", type="string", nullable=false)
   */
  protected $thumbSm;

  /**
   * @var string
   * @ORM\Column(name="thumb_md", type="string", nullable=false)
   */
  protected $thumbMd;

  /**
   * @var string
   * @ORM\Column(name="thumb_lg", type="string", nullable=false)
   */
  protected $thumbLg;

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
    if (!Providers::isValidProvider($provider)) {
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
