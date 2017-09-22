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

  /**
   * @covers ::parseURL
   */
    public function testYoutubeVideo()
    {
        $expected = [
        "codename" => "fS9f6_i6v3Q",
        "provider" => Video::PROVIDER_YOUTUBE,
        "playlist" => false
        ];

        $mediaURL = "https://www.youtube.com/watch?v=fS9f6_i6v3Q&feature=youtu.be";
        $actual   = $this->providers->parseURL($mediaURL);
        $this->assertEquals($expected, $actual);

        $mediaURL = "https://youtu.be/fS9f6_i6v3Q";
        $actual   = $this->providers->parseURL($mediaURL);
        $this->assertEquals($expected, $actual);
    }

  /**
   * @covers ::parseURL
   */
    public function testYoutubePlaylist()
    {
        $expected = [
        "codename" => "PL5D7fjEEs5yeDL2KZ7517GK5gPR9Kb7vb",
        "provider" => Video::PROVIDER_YOUTUBE,
        "playlist" => true
        ];

        $mediaURL = "https://www.youtube.com/playlist?list=PL5D7fjEEs5yeDL2KZ7517GK5gPR9Kb7vb";
        $actual   = $this->providers->parseURL($mediaURL);
        $this->assertEquals($expected, $actual);

        $mediaURL = "https://www.youtube.com/watch?v=-MsvER1dpjM&list=PL5D7fjEEs5yeDL2KZ7517GK5gPR9Kb7vb";
        $actual   = $this->providers->parseURL($mediaURL);
        $this->assertEquals($expected, $actual);
    }
}
