<?php
namespace AppBundle\EventListener\Socket;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class PMRequestEvent extends Event
{
  /**
   * @var UserInterface|User
   */
  protected $user;

  /**
   * @var array
   */
  protected $payload = [];

  /**
   * Constructor
   *
   * @param UserInterface|User $user
   * @param array $payload
   */
  public function __construct(UserInterface $user, array $payload)
  {
    $this->user    = $user;
    $this->payload = $payload;
  }

  /**
   * @return array
   */
  public function getPayload()
  {
    return $this->payload;
  }

  /**
   * @param array $payload
   * @return $this
   */
  public function setPayload(array $payload)
  {
    $this->payload = $payload;
    return $this;
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
}
