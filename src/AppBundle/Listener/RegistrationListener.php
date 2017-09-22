<?php
namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Entity\UserInfo;
use AppBundle\Service\ThumbsService;
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
   * @var ThumbsService
   */
    protected $thumbService;

  /**
   * Constructor
   *
   * @param ThumbsService $thumbsService
   */
    public function __construct(ThumbsService $thumbsService)
    {
        $this->thumbService = $thumbsService;
    }

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
        $info = new UserInfo();
        $info->setUser($this->user);
        $info->setAvatarSm($this->thumbService->getUserAvatar($this->user, "sm"));
        $info->setAvatarMd($this->thumbService->getUserAvatar($this->user, "md"));
        $info->setAvatarLg($this->thumbService->getUserAvatar($this->user, "lg"));
        $this->user->setInfo($info);
    }
}
