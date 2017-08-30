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
      Event\UserEvents::PLAYED_VIDEO => "onPlayedVideo"
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
}
