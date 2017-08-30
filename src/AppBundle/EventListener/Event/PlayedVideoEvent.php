<?php
namespace AppBundle\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;

/**
 * Triggered when a user plays a video.
 */
class PlayedVideoEvent extends Event
{
  /**
   * @var Room
   */
  protected $room;

  /**
   * @var Video
   */
  protected $video;

  /**
   * @var User
   */
  protected $user;

  /**
   * Constructor
   *
   * @param User $user
   * @param Room $room
   * @param Video $video
   */
  public function __construct(User $user, Room $room, Video $video)
  {
    $this->setUser($user);
    $this->setRoom($room);
    $this->setVideo($video);
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
   * @return Video
   */
  public function getVideo()
  {
    return $this->video;
  }

  /**
   * @param Video $video
   * @return $this
   */
  public function setVideo($video)
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
   * @return $this
   */
  public function setUser(User $user)
  {
    $this->user = $user;
    return $this;
  }
}
