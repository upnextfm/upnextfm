<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
  /**
   * @var EntityManagerInterface
   */
    protected $em;

  /**
   * Constructor
   *
   * @param EntityManagerInterface $em
   */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

  /**
   * {@inheritdoc}
   */
    public static function getSubscribedEvents()
    {
        return [
        Event\UserEvents::PLAYED_VIDEO => "onPlayedVideo",
        Event\UserEvents::FAVORITED    => "onFavorited",
        Event\UserEvents::UPVOTED      => "onUpvoted",
        Event\UserEvents::CREATED_ROOM => "onCreatedRoom"
        ];
    }

  /**
   * @param Event\PlayedVideoEvent $event
   */
    public function onPlayedVideo(Event\PlayedVideoEvent $event)
    {
        $userEvent = new UserEvent(Event\UserEvents::PLAYED_VIDEO);
        $userEvent->setUser($event->getUser());
        $userEvent->setTargetRoom($event->getRoom());
        $userEvent->setTargetVideo($event->getVideo());
        $this->em->persist($userEvent);
        $this->em->flush();
    }

  /**
   * @param Event\FavoritedEvent $event
   */
    public function onFavorited(Event\FavoritedEvent $event)
    {
        $userEvent = new UserEvent(Event\UserEvents::FAVORITED);
        $userEvent->setUser($event->getUser());
        $userEvent->setTargetRoom($event->getRoom());
        $userEvent->setTargetVideo($event->getVideo());
        $this->em->persist($userEvent);
        $this->em->flush();
    }

  /**
   * @param Event\UpvotedEvent $event
   */
    public function onUpvoted(Event\UpvotedEvent $event)
    {
        $userEvent = new UserEvent(Event\UserEvents::UPVOTED);
        $userEvent->setUser($event->getUser());
        $userEvent->setTargetRoom($event->getRoom());
        $userEvent->setTargetVideo($event->getVideo());
        $this->em->persist($userEvent);
        $this->em->flush();
    }

  /**
   * @param Event\CreatedRoomEvent $event
   */
    public function onCreatedRoom(Event\CreatedRoomEvent $event)
    {
        $userEvent = new UserEvent(Event\UserEvents::CREATED_ROOM);
        $userEvent->setUser($event->getUser());
        $userEvent->setTargetRoom($event->getRoom());
        $this->em->persist($userEvent);
        $this->em->flush();
    }
}
