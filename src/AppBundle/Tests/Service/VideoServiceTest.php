<?php
namespace AppBundle\Tests\Service;

use AppBundle\Playlist\Providers;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Service\VideoService;
use AppBundle\Service\VideoInfo;
use Madcoda\Youtube\Youtube;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass AppBundle\Service\VideoService
 */
class VideoServiceTest extends KernelTestCase
{
  /**
   * @var VideoService
   */
  protected $service;

  /**
   * Called before each test
   */
  protected function setUp()
  {
    self::bootKernel();
    $container = self::$kernel->getContainer();

    /* @var LoggerInterface $logger */
    $settings = ["maxPlaylistSize" => 50];
    $logger   = $this->getMockBuilder(LoggerInterface::class)->getMock();
    $this->service = new VideoService($settings, $logger);

    $options = [
      "key" => $container->getParameter("app_service_youtube_api_key")
    ];
    $youtube = new Youtube($options);
    $youtube->setReferer("https://upnext.fm");
    $this->service->setYoutube($youtube);
  }

  /**
   * @covers ::getInfo
   */
  public function testGetInfo()
  {
    $this->markTestSkipped();
    $codename = "Tv9YoYCKNoE";
    $provider = Providers::YOUTUBE;

    $actual = $this->service->getInfo($codename, $provider);
    $this->assertInstanceOf(VideoInfo::class, $actual);
    $this->assertEquals($codename, $actual->getCodename());
    $this->assertEquals($provider, $actual->getProvider());
    $this->assertEquals("Grimes - Flesh without Blood/Life in the Vivid Dream", $actual->getTitle());
    $this->assertEquals("https://youtu.be/Tv9YoYCKNoE", $actual->getPermalink());
    $this->assertEquals(412, $actual->getSeconds());
    $this->assertEquals("c1b4b5", $actual->getThumbColor());
    $this->assertNotEmpty($actual->getThumbnail("sm"));
    $this->assertNotEmpty($actual->getThumbnail("md"));
    $this->assertNotEmpty($actual->getThumbnail("lg"));
  }
}
