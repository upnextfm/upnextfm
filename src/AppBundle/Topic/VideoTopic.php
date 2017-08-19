<?php
namespace AppBundle\Topic;

use AppBundle\Entity\Room;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Security\Core\User\UserInterface;

class VideoTopic extends AbstractTopic
{
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
  public function onPublish(
    ConnectionInterface $conn,
    Topic $topic,
    WampRequest $req,
    $event,
    array $exclude,
    array $eligible)
  {
    try {
      $user = $this->getUser($conn);
      if (is_string($user)) {
        return;
      }
      $room = $this->getRoom($req->getAttributes()->get("room"), $user);
      if (!$room || $room->isDeleted()) {
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
   * @param UserInterface $user
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
    dump([
      "cmd"     => VideoCommands::START,
      "videoID" => $event["videoID"]
    ]);
    $topic->broadcast([
      "cmd"     => VideoCommands::START,
      "videoID" => $event["videoID"]
    ]);
  }
}
