<?php
namespace AppBundle\Service;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;

class ThumbsService
{
  /**
   * @var array
   */
  protected $defaults = [];

  /**
   * Constructor
   *
   * @param array $defaults
   */
  public function __construct(array $defaults)
  {
    $this->defaults = $defaults;
  }

  /**
   * @param User $user
   * @param string $size
   * @return string
   */
  public function getUserAvatar(User $user, $size)
  {
    if (!$user || !$user->getInfo()) {
      return str_replace("{name}", $user->getUsername(), $this->defaults["avatar_${size}"]);
    }

    switch($size) {
      case "sm":
        $avatar = $user->getInfo()->getAvatarSm();
        break;
      case "md":
        $avatar = $user->getInfo()->getAvatarMd();
        break;
      case "lg":
        $avatar = $user->getInfo()->getAvatarLg();
        break;
      default:
        throw new \InvalidArgumentException("Invalid avatar size '${size}'.");
        break;
    }
    if ($avatar) {
      return $avatar;
    }

    return str_replace("{name}", $user->getUsername(), $this->defaults["avatar_${size}"]);
  }

  /**
   * @param Room $room
   * @param string $size
   * @return string
   */
  public function getRoomThumb(Room $room, $size)
  {
    if (!$room || !$room->getSettings()) {
      return str_replace("{name}", $room->getName(), $this->defaults["thumb_${size}"]);
    }

    switch($size) {
      case "sm":
        $thumb = $room->getSettings()->getThumbSm();
        break;
      case "md":
        $thumb = $room->getSettings()->getThumbMd();
        break;
      case "lg":
        $thumb = $room->getSettings()->getThumbLg();
        break;
      default:
        throw new \InvalidArgumentException("Invalid thumb size '${size}'.");
        break;
    }
    if ($thumb) {
      return $thumb;
    }

    return str_replace("{name}", $room->getName(), $this->defaults["thumb_${size}"]);
  }
}
