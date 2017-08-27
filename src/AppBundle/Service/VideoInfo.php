<?php
namespace AppBundle\Service;

class VideoInfo
{
  /**
   * @var string
   */
  protected $codename;

  /**
   * @var string
   */
  protected $provider;

  /**
   * @var string
   */
  protected $permalink;

  /**
   * @var string
   */
  protected $title = "";

  /**
   * @var string
   */
  protected $description = "";

  /**
   * @var int
   */
  protected $seconds = 0;

  /**
   * @var string
   */
  protected $thumbColor = "000000";

  /**
   * @var array
   */
  protected $thumbnails = [
    "sm" => "",
    "md" => "",
    "lg" => ""
  ];

  /**
   * Constructor
   *
   * @param string $codename
   * @param string $provider
   * @param string $permalink
   */
  public function __construct($codename, $provider, $permalink)
  {
    $this->setCodename($codename);
    $this->setProvider($provider);
    $this->setPermalink($permalink);
  }

  /**
   * @return string
   */
  public function getCodename()
  {
    return $this->codename;
  }

  /**
   * @param string $codename
   * @return $this
   */
  public function setCodename($codename)
  {
    $this->codename = $codename;
    return $this;
  }

  /**
   * @return string
   */
  public function getProvider()
  {
    return $this->provider;
  }

  /**
   * @param string $provider
   * @return $this
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
    return $this;
  }

  /**
   * @return string
   */
  public function getPermalink()
  {
    return $this->permalink;
  }

  /**
   * @param string $permalink
   * @return $this
   */
  public function setPermalink($permalink)
  {
    $this->permalink = $permalink;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * @param string $title
   * @return $this
   */
  public function setTitle($title)
  {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $description
   * @return $this
   */
  public function setDescription($description)
  {
    $this->description = $description;
    return $this;
  }

  /**
   * @return int
   */
  public function getSeconds()
  {
    return $this->seconds;
  }

  /**
   * @param int $seconds
   * @return $this
   */
  public function setSeconds($seconds)
  {
    $this->seconds = $seconds;
    return $this;
  }

  /**
   * @return string
   */
  public function getThumbColor()
  {
    return $this->thumbColor;
  }

  /**
   * @param string $thumbColor
   * @return $this
   */
  public function setThumbColor($thumbColor)
  {
    $this->thumbColor = $thumbColor;
    return $this;
  }

  /**
   * @param string $size
   * @return string
   */
  public function getThumbnail($size = "md")
  {
    return $this->thumbnails[$size];
  }

  /**
   * @param string $size
   * @param string $url
   * @return $this
   */
  public function setThumbnail($size, $url)
  {
    $this->thumbnails[$size] = $url;
    return $this;
  }

  /**
   * @return array
   */
  public function getThumbnails()
  {
    return $this->thumbnails;
  }

  /**
   * @param array $thumbnails
   * @return $this
   */
  public function setThumbnails($thumbnails)
  {
    $this->thumbnails = $thumbnails;
    return $this;
  }
}
