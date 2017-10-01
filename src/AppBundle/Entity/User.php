<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @var UserInfo
   *
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserInfo", mappedBy="user", fetch="EAGER", cascade={"persist"})
   */
  protected $info;

  /**
   * @var UserSettings
   *
   * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserSettings", mappedBy="user", fetch="EAGER", cascade={"persist"})
   */
  protected $settings;

  /**
   * @var boolean
   *
   * @ORM\Column(name="is_anonymous", type="boolean", nullable=false)
   */
  protected $isAnonymous = false;

  /**
   * Constructor
   *
   * @param string $username
   * @param bool $isAnonymous
   */
  public function __construct($username = "", $isAnonymous = false)
  {
    parent::__construct();
    $this->setUsername($username);
    $this->setUsernameCanonical($username);
    $this->setIsAnonymous($isAnonymous);
    $this->info = new UserInfo();
  }

  /**
   * @return UserInfo
   */
  public function getInfo()
  {
    return $this->info;
  }

  /**
   * @param UserInfo $info
   * @return $this
   */
  public function setInfo(UserInfo $info)
  {
    $this->info = $info;
    return $this;
  }

  /**
   * @return UserSettings
   */
  public function getSettings()
  {
    return $this->settings;
  }

  /**
   * @param UserSettings $settings
   * @return $this
   */
  public function setSettings(UserSettings $settings)
  {
    $this->settings = $settings;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsAnonymous()
  {
    return $this->isAnonymous;
  }

  /**
   * @param bool $isAnonymous
   * @return $this
   */
  public function setIsAnonymous($isAnonymous)
  {
    $this->isAnonymous = $isAnonymous;
    return $this;
  }
}
