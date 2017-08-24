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
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\UserInfo", mappedBy="user", fetch="EAGER", cascade={"persist"})
     */
    protected $info;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
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
}
