<?php
namespace AppBundle\Topic;

use AppBundle\Entity\Room;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoLog;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoTopic extends AbstractTopic
{
  /**
   * @var array
   */
  protected $playing = [];

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return "video.topic";
  }

  /**
   * {@inheritdoc}
   */
  public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
  {
    $user = $this->getUser($connection);
    if (!($user instanceof UserInterface)) {
      $user = null;
    }
    $room = $this->getRoom($request->getAttributes()->get("room"), $user);
    if (!$room) {
      $this->logger->error("Room not found or created.");
      return;
    }

    if (isset($this->playing[$room->getId()])) {
      $video = $this->playing[$room->getId()];
      $connection->event($topic->getId(), [
        "cmd"      => VideoCommands::START,
        "codename" => $video->getCodename(),
        "provider" => $video->getProvider()
      ]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function onPublish(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    $event,
    array $exclude,
    array $eligible)
  {
    try {
      $event = array_map("trim", $event);
      if (empty($event["cmd"])) {
        $this->logger->error("cmd not set.", $event);
        return;
      }
      $user = $this->getUser($conn);
      if (!($user instanceof UserInterface)) {
        $this->logger->error("User not found.", $event);
        return;
      }
      $room = $this->getRoom($req->getAttributes()->get("room"), $user);
      if (!$room || $room->isDeleted()) {
        $this->logger->error("Room not found.", $event);
        return;
      }

      switch ($event["cmd"]) {
        case VideoCommands::PLAY:
          $this->handlePlay($conn, $topic, $req, $room, $user, $event);
          break;
      }
    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }
  }

  /**
   * @param ConnectionInterface $conn
   * @param Topic $topic
   * @param WampRequest $req
   * @param Room $room
   * @param UserInterface|User $user
   * @param array $event
   */
  protected function handlePlay(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    Room $room,
    UserInterface $user,
    array $event)
  {
    if (empty($event["codename"]) || empty($event["provider"])) {
      $this->logger->error("Missing event argument.", $event);
      return;
    }
    if (!Video::isValidProvider($event["provider"])) {
      $this->logger->error("Invalid provider.", $event);
      return;
    }

    $video = $this->em->getRepository("AppBundle:Video")
      ->findByCodename($event["codename"], $event["provider"]);
    if (!$video) {
      $service = $this->container->get("app.service.video");
      $info    = $service->getInfo($event["codename"], $event["provider"]);
      if (!$info) {
        $this->logger->error("Failed to fetch video info.", $event);
        return;
      }

      $video = new Video();
      $video->setCodename($event["codename"]);
      $video->setProvider($event["provider"]);
      $video->setCreatedByUser($user);
      $video->setCreatedInRoom($room);
      $video->setTitle($info->getTitle());
      $video->setSeconds($info->getSeconds());
      $video->setNumPlays(0);
    }
    $video->setDateLastPlayed(new \DateTime());
    $video->incrNumPlays();
    $this->em->persist($video);
    $this->playing[$room->getId()] = $video;

    $videoLog = new VideoLog($video, $room, $user);
    $this->em->merge($videoLog);
    $this->em->flush();

    $topic->broadcast([
      "cmd"      => VideoCommands::START,
      "codename" => $video->getCodename(),
      "provider" => $video->getProvider()
    ]);
  }
}
