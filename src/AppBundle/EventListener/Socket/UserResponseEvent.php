<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class UserResponseEvent extends Event
{
  /**
   * @var User|UserInterface
   */
  protected $user;

  /**
   * @var string
   */
  protected $action;

  /**
   * @var array
   */
  protected $args = [];

  /**
   * Constructor
   *
   * @param UserInterface|User $user
   * @param string $action
   * @param array $args
   */
  public function __construct(UserInterface $user, $action, array $args = [])
  {
    $this->user   = $user;
    $this->action = $action;
    $this->args   = $args;
  }

  /**
   * @return UserInterface|User
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * @param UserInterface|User $user
   * @return $this
   */
  public function setUser($user)
  {
    $this->user = $user;
    return $this;
  }

  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }

  /**
   * @param string $action
   * @return RoomResponseEvent
   */
  public function setAction($action)
  {
    $this->action = $action;
    return $this;
  }

  /**
   * @return array
   */
  public function getArgs()
  {
    return $this->args;
  }

  /**
   * @param array $args
   * @return RoomResponseEvent
   */
  public function setArgs($args)
  {
    $this->args = $args;
    return $this;
  }
}
