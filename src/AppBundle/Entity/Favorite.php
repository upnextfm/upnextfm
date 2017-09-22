<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FavoriteRepository")
 * @ORM\Table(name="favorite")
 */
class Favorite
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
   * @var \AppBundle\Entity\Video
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Video", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="video_id", referencedColumnName="id")
   * })
   */
    protected $video;

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
   *
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
    protected $dateCreated;

  /**
   * Constructor
   */
    public function __construct()
    {
        $this->dateUpdated = new DateTime();
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
   * @return Favorite
   */
    public function setVideo(Video $video)
    {
        $this->video = $video;
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
   * @return Favorite
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
   * @return Favorite
   */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }
}
