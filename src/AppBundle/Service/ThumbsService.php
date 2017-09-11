<?php
namespace AppBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use Identicon\Identicon;
use Imagick;

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
  protected $settings = [];

  /**
   * Constructor
   *
   * @param Identicon $identicon
   * @param UploadService $uploadService
   * @param array $settings
   */
  public function __construct(Identicon $identicon, UploadService $uploadService, array $settings)
  {
    $this->identicon     = $identicon;
    $this->uploadService = $uploadService;
    $this->settings      = $settings;
  }

  /**
   * Create thumbs in the 3 sizes, sm, md, and lg
   *
   * @param string $imagePath
   * @param string $extension
   * @return array
   */
  public function create($imagePath, $extension = 'png')
  {
    $tempFiles = [];
    foreach($this->settings["sizes"] as $name => $width) {
      $tempFiles[$name] = sprintf(
        '%s.%s',
        tempnam(sys_get_temp_dir(), "thumb"),
        $extension
      );

      $imagick = new Imagick($imagePath);
      $imagick->resizeImage($width, $width, Imagick::FILTER_CATROM, 1, true);
      $imagick->writeImage($tempFiles[$name]);
      $imagick->destroy();
    }

    return $tempFiles;
  }

  /**
   * @param User $user
   * @param string $size
   * @return string
   */
  public function getUserAvatar(User $user, $size)
  {
    if (!$user || !$user->getInfo()) {
      return str_replace("{name}", $user->getUsername(), $this->settings["avatar_${size}"]);
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

    return str_replace("{name}", $user->getUsername(), $this->settings["avatar_${size}"]);
  }

  /**
   * @param Room $room
   * @param UserInterface $user
   * @param string $size
   * @return string
   */
  public function getRoomThumb(Room $room, UserInterface $user, $size)
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
    $thumbs   = [
      "sm" => sprintf("%s/thumb-sm.png", $roomName),
      "md" => sprintf("%s/thumb-md.png", $roomName),
      "lg" => sprintf("%s/thumb-lg.png", $roomName)
    ];
    foreach($thumbs as $s => $path) {
      $imageData = $this->identicon->getImageData($roomName, $this->settings["sizes"][$s]);
      $urls[$s]  = $this->uploadService->uploadData($imageData, $path, $user, "image/png");
    }

    return $urls[$size];
  }
}
