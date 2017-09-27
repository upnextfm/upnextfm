<?php
namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Vote
 *
 * @ORM\Table(name="vote",uniqueConstraints={@UniqueConstraint(name="user_vote", columns={"video_id", "user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\VoteRepository")
 * @UniqueEntity(fields={"video", "user"}, message="You have already voted on this video.")
 */
class Vote
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
    private $id;

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
   * @var int
   *
   * @ORM\Column(name="value", type="smallint")
   */
    private $value;

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
        $this->dateCreated = new DateTime();
    }

  /**
   * Get id
   *
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
   * @return Vote
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
   * @return Vote
   */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }


  /**
   * Set value
   *
   * @param integer $value
   *
   * @return Vote
   */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

  /**
   * Get value
   *
   * @return int
   */
    public function getValue()
    {
        return $this->value;
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
   * @return Vote
   */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }
}
