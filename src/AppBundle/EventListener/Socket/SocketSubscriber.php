<?php
namespace AppBundle\EventListener\Socket;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Dispatches client side events to backend listeners.
 */
class SocketSubscriber implements EventSubscriberInterface
{
  use LoggerAwareTrait;

  /**
   * @var VideoListener
   */
  protected $videoListener;

  /**
   * @var RoomListener
   */
  protected $roomListener;

  /**
   * @var PMListener
   */
  protected $pmListener;

  /**
   * @param VideoListener $videoListener
   * @return $this
   */
  public function setVideoListener(VideoListener $videoListener)
  {
    $this->videoListener = $videoListener;
    return $this;
  }

  /**
   * @param RoomListener $roomListener
   * @return $this
   */
  public function setRoomListener(RoomListener $roomListener)
  {
    $this->roomListener = $roomListener;
    return $this;
  }

  /**
   * @param PMListener $pmListener
   * @return $this
   */
  public function setPMListener(PMListener $pmListener)
  {
    $this->pmListener = $pmListener;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      SocketEvents::VIDEO_REQUEST => "onVideoRequest",
      SocketEvents::ROOM_REQUEST  => "onRoomRequest",
      SocketEvents::PM_REQUEST    => "onPMRequest"
    ];
  }

  /**
   * @param VideoRequestEvent $event
   */
  public function onVideoRequest(VideoRequestEvent $event)
  {
    $payload = $event->getPayload();
    $this->dispatchEvent($this->videoListener, $payload, [
      $event->getRoom(),
      $event->getUser()
    ]);
  }

  /**
   * Handles the SocketEvents::ROOM_REQUEST event
   *
   * @param RoomRequestEvent $event
   */
  public function onRoomRequest(RoomRequestEvent $event)
  {
    $payload = $event->getPayload();
    $this->dispatchEvent($this->roomListener, $payload, [
      $event->getUser(),
      $event->getRoom()
    ]);
  }

  /**
   * Handles the SocketEvents::PM_REQUEST event
   *
   * @param PMRequestEvent $event
   */
  public function onPMRequest(PMRequestEvent $event)
  {
    $payload = $event->getPayload();
    $this->dispatchEvent($this->pmListener, $payload, [
      $event->getUser()
    ]);
  }

  /**
   * @param AbstractListener $listener
   * @param string $payload
   * @param array $args
   */
  private function dispatchEvent($listener, $payload, array $args)
  {
    if (!isset($payload["dispatch"])) {
      throw new \RuntimeException("Invalid payload contains no 'dispatch' property.");
    }

    foreach($payload["dispatch"] as $action) {
      $method = ucwords($action["action"]);
      $method = "on${method}";
      if (!method_exists($listener, $method)) {
        throw new \RuntimeException(sprintf(
          'Method "%s" not found on listener.',
          $method
        ));
      }

      call_user_func_array(
        [$listener, $method],
        array_merge($args, $action["args"])
      );
    }
  }
}
