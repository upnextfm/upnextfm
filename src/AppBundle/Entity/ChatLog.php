<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ChatLogRepository")
 * @ORM\Table(name="chat_log")
 */
class ChatLog
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
   * @ORM\Column(name="message", type="text", nullable=true)
   */
  protected $message = "";

  /**
   * @var \AppBundle\Entity\Room
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Room")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="room_id", referencedColumnName="id")
   * })
   */
  protected $room;

  /**
   * @var \AppBundle\Entity\User
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   * })
   */
  protected $user;

  /**
   * @var DateTime
   * @ORM\Column(name="date_created", type="datetime", nullable=false)
   */
  protected $dateCreated;

  /**
   * Constructor
   *
   * @param Room $room
   * @param User $user
   * @param string $message
   */
  public function __construct($room = null, $user = null, $message = "")
  {
    $this->room        = $room;
    $this->user        = $user;
    $this->message     = $message;
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
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @param string $message
   * @return $this
   */
  public function setMessage($message)
  {
    $this->message = $message;
    return $this;
  }

  /**
   * @return Room
   */
  public function getRoom()
  {
    return $this->room;
  }

  /**
   * @param Room $room
   * @return $this
   */
  public function setRoom($room)
  {
    $this->room = $room;
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
   * @return $this
   */
  public function setUser($user)
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
   * @return $this
   */
  public function setDateCreated($dateCreated)
  {
    $this->dateCreated = $dateCreated;
    return $this;
  }
}
