<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UploadRepository")
 * @ORM\Table(name="upload")
 */
class Upload
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
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   * })
   */
    protected $user;

  /**
   * @var int
   *
   * @ORM\Column(name="size", type="integer", nullable=false)
   */
    protected $size = 0;

  /**
   * @var string
   *
   * @ORM\Column(name="mime", type="string", length=25, nullable=false)
   */
    protected $mime;

  /**
   * @var string
   *
   * @ORM\Column(name="path", type="string", length=200, nullable=false)
   */
    protected $path;

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
   * @return $this
   */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

  /**
   * @return int
   */
    public function getSize()
    {
        return $this->size;
    }

  /**
   * @param int $size
   * @return $this
   */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

  /**
   * @return string
   */
    public function getMime()
    {
        return $this->mime;
    }

  /**
   * @param string $mime
   * @return $this
   */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

  /**
   * @return string
   */
    public function getPath()
    {
        return $this->path;
    }

  /**
   * @param string $path
   * @return $this
   */
    public function setPath($path)
    {
        $this->path = $path;
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
