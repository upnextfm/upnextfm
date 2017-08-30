<?php
namespace AppBundle\Service;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Identicon\Identicon;

class ThumbsService
{
  /**
   * @var Identicon
   */
  protected $identicon;

  /**
   * @var UploadService
   */
  protected $uploadService;

  /**
   * @var array
   */
  protected $defaults = [];

  /**
   * Constructor
   *
   * @param Identicon $identicon
   * @param UploadService $uploadService
   * @param array $defaults
   */
  public function __construct(Identicon $identicon, UploadService $uploadService, array $defaults)
  {
    $this->identicon     = $identicon;
    $this->uploadService = $uploadService;
    $this->defaults      = $defaults;
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
    if ($room && $room->getSettings()) {
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
    }

    $roomName = $room->getName();
    $urls     = [];
    $sizes    = ["sm" => 50, "md" => 250, "lg" => 500];
    $thumbs   = [
      "sm" => sprintf("%s/thumb-sm.png", $roomName),
      "md" => sprintf("%s/thumb-md.png", $roomName),
      "lg" => sprintf("%s/thumb-lg.png", $roomName)
    ];
    foreach($thumbs as $s => $path) {
      $imageData = $this->identicon->getImageData($roomName, $sizes[$s]);
      $urls[$s]  = $this->uploadService->uploadData($imageData, $path);
    }

    return $urls[$size];
  }
}
