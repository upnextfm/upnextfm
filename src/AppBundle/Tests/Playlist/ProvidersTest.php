<?php
namespace AppBundle\Tests\Playlist;

use AppBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use AppBundle\Playlist\Providers;

/**
 * @coversDefaultClass AppBundle\Playlist\Providers
 */
class ProvidersTest extends TestCase
{
  /**
   * @var Providers
   */
  protected $providers;

  /**
   * Called before each test
   */
  protected function setUp()
  {
    $this->providers = new Providers();
  }

  public function testYoutube()
  {
    $expected = [
      "codename" => "fS9f6_i6v3Q",
      "provider" => Video::PROVIDER_YOUTUBE
    ];

    $mediaURL = "https://www.youtube.com/watch?v=fS9f6_i6v3Q&feature=youtu.be";
    $actual   = $this->providers->parseURL($mediaURL);
    $this->assertEquals($expected, $actual);

    $mediaURL = "https://youtu.be/fS9f6_i6v3Q";
    $actual   = $this->providers->parseURL($mediaURL);
    $this->assertEquals($expected, $actual);
  }
}
