<?php
namespace AppBundle\EventListener\Socket;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SocketSubscriber implements EventSubscriberInterface
{
  /**
   * @var RoomListener
   */
  protected $roomListener;

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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      SocketEvents::ROOM_REQUEST => "onRoom"
    ];
  }

  /**
   * Handles the SocketEvents::ROOM_REQUEST event
   *
   * @param RoomRequestEvent $event
   */
  public function onRoom(RoomRequestEvent $event)
  {
    $payload = $event->getPayload();
    $user    = $event->getUser();
    $room    = $event->getRoom();

    foreach($payload["dispatch"] as $action) {
      $method = ucwords($action["action"]);
      $method = "on${method}";
      $args   = array_merge([$user, $room], $action["args"]);
      call_user_func_array([$this->roomListener, $method], $args);
    }
  }
}
