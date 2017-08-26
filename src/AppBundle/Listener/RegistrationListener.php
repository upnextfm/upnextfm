<?php
namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationListener implements EventSubscriberInterface
{
  /**
   * @var UserInterface|User
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    return [
      FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
      FOSUserEvents::REGISTRATION_SUCCESS    => "onRegistrationSuccess"
    ];
  }

  /**
   * @param GetResponseUserEvent $event
   */
  public function onRegistrationInitialize(GetResponseUserEvent $event)
  {
    $this->user = $event->getUser();
  }

  /**
   * @param FormEvent $event
   */
  public function onRegistrationSuccess(FormEvent $event)
  {
    $username = $this->user->getUsername();
    $info = new UserInfo();
    $info->setUser($this->user);
    $info->setAvatarSm(sprintf('https://robohash.org/%s?set=set3&size=40x40', $username));
    $info->setAvatarMd(sprintf('https://robohash.org/%s?set=set3&size=100x100', $username));
    $info->setAvatarLg(sprintf('https://robohash.org/%s?set=set3&size=250x250', $username));
    $this->user->setInfo($info);
  }
}
