<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PrivateMessageRepository")
 * @ORM\Table(name="private_message")
 */
class PrivateMessage
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
   * @ORM\Column(name="message", type="string", length=1000, nullable=false)
   */
  protected $message = "";

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
   * })
   */
  protected $fromUser;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", fetch="EAGER")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
   * })
   */
  protected $toUser;

  /**
   * @var DateTime
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
   * @return mixed
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param mixed $message
   * @return $this
   */
  public function setMessage($message)
  {
    $this->message = $message;
    return $this;
  }

  /**
   * @return User
   */
  public function getFromUser()
  {
    return $this->fromUser;
  }

  /**
   * @param User $fromUser
   * @return $this
   */
  public function setFromUser($fromUser)
  {
    $this->fromUser = $fromUser;
    return $this;
  }

  /**
   * @return User
   */
  public function getToUser()
  {
    return $this->toUser;
  }

  /**
   * @param User $toUser
   * @return $this
   */
  public function setToUser($toUser)
  {
    $this->toUser = $toUser;
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
