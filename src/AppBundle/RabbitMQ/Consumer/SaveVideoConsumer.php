<?php
namespace AppBundle\RabbitMQ\Consumer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use AppBundle\Service\VideoInfo;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoLog;

class SaveVideoConsumer implements ConsumerInterface
{
  /**
   * @var ContainerInterface
   */
  protected $container;

  /**
   * @var EntityManagerInterface
   */
  protected $em;

  /**
   * @var \AppBundle\Entity\UserRepository
   */
  protected $repoUser;

  /**
   * @var \AppBundle\Entity\VideoRepository
   */
  protected $repoVideo;

  /**
   * @var \AppBundle\Entity\RoomRepository
   */
  protected $repoRoom;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * Constructor
   *
   * @param ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
    $this->em        = $container->get("doctrine.orm.default_entity_manager");
    $this->repoUser  = $this->em->getRepository("AppBundle:User");
    $this->repoVideo = $this->em->getRepository("AppBundle:Video");
    $this->repoRoom  = $this->em->getRepository("AppBundle:Room");
    $this->logger    = $container->get("logger");
  }

  /**
   * @param AMQPMessage $msg The message
   * @return mixed false to reject and requeue, any other value to acknowledge
   */
  public function execute(AMQPMessage $msg)
  {
    $body = json_decode($msg->body, true);
    dump($body);

    $video = $this->repoVideo->findByCodename($body["codename"], $body["provider"]);
    $user  = $this->repoUser->findByID($body["user_id"]);
    $room  = $this->repoRoom->findByID($body["room_id"]);
    if (!$user || !$room) {
      echo "User or room not found.\n";
      return true;
    }

    if (!$video) {
      echo "Video not found. Creating.\n";

      $info = $this->getVideoInfo($body["codename"], $body["provider"]);
      if (!$info) {
        echo "Failed to fetch video info.\n";
        return false;
      }

      $video = new Video();
      $video->setCodename($body["codename"]);
      $video->setProvider($body["provider"]);
      $video->setCreatedByUser($user);
      $video->setCreatedInRoom($room);
      $video->setTitle($info->getTitle());
      $video->setSeconds($info->getSeconds());
      $video->setPermalink($info->getPermalink());
      $video->setThumbColor($info->getThumbColor());
      $video->setThumbSm($info->getThumbnail("sm"));
      $video->setThumbMd($info->getThumbnail("md"));
      $video->setThumbLg($info->getThumbnail("lg"));
      $video->setNumPlays(0);
      $this->em->persist($video);
      $this->em->flush();
    }

    if ($body["video_log"]) {
      $video->setDateLastPlayed(new \DateTime());
      $video->incrNumPlays();
      $videoLog = new VideoLog($video, $room, $user);
      $this->em->persist($videoLog);
      $this->em->flush();
    }

    return true;
  }

  /**
   * @param string $codename
   * @param string $provider
   * @return VideoInfo|bool
   */
  protected function getVideoInfo($codename, $provider)
  {
    $service = $this->container->get("app.service.video");
    $info    = $service->getInfo($codename, $provider);
    if (!$info) {
      echo "Failed to fetch video info.\n";
      return false;
    }

    return $info;
  }
}
