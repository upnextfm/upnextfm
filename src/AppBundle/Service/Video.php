<?php
namespace AppBundle\Service;

use Madcoda\Youtube\Youtube;
use Psr\Log\LoggerInterface;

class Video
{
  /**
   * @var Youtube
   */
  protected $youtube;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * Constructor
   *
   * @param LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  /**
   * @param Youtube $youtube
   * @return $this
   */
  public function setYoutube(Youtube $youtube)
  {
    $this->youtube = $youtube;
    return $this;
  }

  /**
   * @param string $codename
   * @param string $provider
   * @return VideoInfo
   */
  public function getInfo($codename, $provider)
  {
    $this->logger->debug(sprintf(
      "Fetching video info for '%s'@'%s'.",
      $codename,
      $provider
    ));

    switch($provider) {
      case "youtube":
        $resp = $this->youtube->getVideoInfo($codename);
        $info = new VideoInfo($codename, $provider, "https://youtu.be/${codename}");
        $info
          ->setTitle($resp->snippet->title)
          ->setSeconds($this->youtubeToSeconds($resp->contentDetails->duration))
          ->setDescription($resp->snippet->description)
          ->setThumbnail("sm", $resp->snippet->thumbnails->medium->url)
          ->setThumbnail("md", $resp->snippet->thumbnails->standard->url)
          ->setThumbnail("lg", $resp->snippet->thumbnails->maxres->url);
        return $info;
        break;
      default:
        return null;
        break;
    }
  }

  /**
   * @param string $duration
   * @return int
   */
  protected function youtubeToSeconds($duration)
  {
    $start = new \DateTime('@0'); // Unix epoch
    $start->add(new \DateInterval($duration));
    return $start->getTimestamp();
  }
}
