<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_info")
 */
class UserInfo
{
  /**
   * @var int
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var User
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\User", inversedBy="info")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
   * })
   */
  protected $user;

  /**
   * @var string
   * @ORM\Column(name="avatar_sm", type="string", nullable=false)
   */
  protected $avatarSm;

  /**
   * @var string
   * @ORM\Column(name="avatar_md", type="string", nullable=false)
   */
  protected $avatarMd;

  /**
   * @var string
   * @ORM\Column(name="avatar_lg", type="string", nullable=false)
   */
  protected $avatarLg;

  /**
   * @var string
   * @ORM\Column(name="location", type="string", length=25, nullable=false)
   */
  protected $location = "";

  /**
   * @var string
   * @ORM\Column(name="website", type="string", length=50, nullable=false)
   */
  protected $website = "";

  /**
   * @var string
   * @ORM\Column(name="bio", type="text", length=2000, nullable=false)
   */
  protected $bio = "";

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
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
   * @return string
   */
  public function getAvatarSm()
  {
    return $this->avatarSm;
  }

  /**
   * @param string $avatarSm
   * @return $this
   */
  public function setAvatarSm($avatarSm)
  {
    $this->avatarSm = $avatarSm;
    return $this;
  }

  /**
   * @return string
   */
  public function getAvatarMd()
  {
    return $this->avatarMd;
  }

  /**
   * @param string $avatarMd
   * @return $this
   */
  public function setAvatarMd($avatarMd)
  {
    $this->avatarMd = $avatarMd;
    return $this;
  }

  /**
   * @return string
   */
  public function getAvatarLg()
  {
    return $this->avatarLg;
  }

  /**
   * @param string $avatarLg
   * @return $this
   */
  public function setAvatarLg($avatarLg)
  {
    $this->avatarLg = $avatarLg;
    return $this;
  }

  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }

  /**
   * @param string $location
   * @return $this
   */
  public function setLocation($location)
  {
    $this->location = $location;
    return $this;
  }

  /**
   * @return string
   */
  public function getWebsite()
  {
    return $this->website;
  }

  /**
   * @param string $website
   * @return $this
   */
  public function setWebsite($website)
  {
    $this->website = $website;
    return $this;
  }

  /**
   * @return string
   */
  public function getBio()
  {
    return $this->bio;
  }

  /**
   * @param string $bio
   * @return $this
   */
  public function setBio($bio)
  {
    $this->bio = $bio;
    return $this;
  }
}
