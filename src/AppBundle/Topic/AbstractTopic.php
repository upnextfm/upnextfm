<?php
namespace AppBundle\Topic;

use AppBundle\Entity\UserSettings;
use AppBundle\Service\ThumbsService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use AppBundle\Entity\ChatLogRepository;
use AppBundle\Entity\RoomRepository;
use AppBundle\Entity\RoomSettings;
use AppBundle\Entity\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Entity\Room;
use AppBundle\EventListener\Event\CreatedRoomEvent;
use AppBundle\EventListener\Event\UserEvents;
use AppBundle\EventListener\Socket\RoomResponseEvent;
use AppBundle\EventListener\Socket\SocketEvents;
use AppBundle\EventListener\Socket\UserResponseEvent;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractTopic implements TopicInterface
{
  /**
   * @var ThumbsService
   */
  protected $thumbsService;

  /**
   * @var EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * @var ClientManipulatorInterface
   */
  protected $clientManipulator;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * @var RoomRepository
   */
  protected $roomRepository;

  /**
   * @var UserRepository
   */
  protected $userRepository;

  /**
   * @var ChatLogRepository
   */
  protected $chatLogRepository;

  /**
   * @param ThumbsService $thumbsService
   * @param EventDispatcherInterface $eventDispatcher
   * @param ClientManipulatorInterface $clientManipulator
   * @param LoggerInterface $logger
   */
  public function __construct(
    ThumbsService $thumbsService,
    EventDispatcherInterface $eventDispatcher,
    ClientManipulatorInterface $clientManipulator,
    LoggerInterface $logger
  )
  {
    $this->thumbsService     = $thumbsService;
    $this->eventDispatcher   = $eventDispatcher;
    $this->clientManipulator = $clientManipulator;
    $this->logger            = $logger;
  }

  /**
   * @param RoomRepository $roomRepository
   * @return $this
   */
  public function setRoomRepository(RoomRepository $roomRepository)
  {
    $this->roomRepository = $roomRepository;
    return $this;
  }

  /**
   * @param UserRepository $userRepository
   * @return $this
   */
  public function setUserRepository(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
    return $this;
  }

  /**
   * @param ChatLogRepository $chatLogRepository
   * @return $this
   */
  public function setChatLogRepository(ChatLogRepository $chatLogRepository)
  {
    $this->chatLogRepository = $chatLogRepository;
    return $this;
  }

  /**
   * @param ConnectionInterface $connection
   * @return User|UserInterface
   */
  protected function getUser(ConnectionInterface $connection)
  {
    $user = $this->clientManipulator->getClient($connection);
    if ($user instanceof UserInterface) {
      $username = $user->getUsername();
      if ($username) {
        $user = $this->userRepository->findByUsername($user->getUsername());
      }
    } else {
      $user = new User($user, true);
    }
    if (!$user->getSettings()) {
      $user->setSettings(new UserSettings());
    }

    return $user;
  }

  /**
   * @param string $roomName
   * @param UserInterface|User $user
   * @return Room
   */
  protected function getRoom($roomName, UserInterface $user = null)
  {
    $room = $this->roomRepository->findByName($roomName);
    if (!$room && $user !== null) {
      $room     = new Room($roomName, $user);
      $settings = new RoomSettings();
      $settings->setRoom($room);
      $settings->setIsPublic(true);
      $settings->setJoinMessage("Welcome to ${roomName}.");
      $settings->setThumbSm($this->thumbService->getRoomThumb($room, $user, "sm"));
      $settings->setThumbMd($this->thumbService->getRoomThumb($room, $user, "md"));
      $settings->setThumbLg($this->thumbService->getRoomThumb($room, $user, "lg"));
      $room->setSettings($settings);
      $this->roomRepository->save($room);

      $event = new CreatedRoomEvent($user, $room);
      $this->eventDispatcher->dispatch(UserEvents::CREATED_ROOM, $event);
    }

    return $room;
  }

  /**
   * @param User $user
   * @param string $action
   * @param array $args
   * @return UserResponseEvent
   */
  protected function dispatchToUser(User $user, $action, array $args = [])
  {
    return $this->eventDispatcher->dispatch(
      SocketEvents::USER_RESPONSE,
      new UserResponseEvent($user, $action, $args)
    );
  }

  /**
   * @param Room $room
   * @param string $action
   * @param array $args
   * @return RoomResponseEvent
   */
  protected function dispatchToRoom(Room $room, $action, array $args = [])
  {
    return $this->eventDispatcher->dispatch(
      SocketEvents::ROOM_RESPONSE,
      new RoomResponseEvent($room, $action, $args)
    );
  }
}
