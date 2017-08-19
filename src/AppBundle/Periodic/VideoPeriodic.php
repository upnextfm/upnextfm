<?php
namespace AppBundle\Periodic;

use Gos\Bundle\WebSocketBundle\Periodic\PeriodicInterface;
use Predis\Client as Redis;

class VideoPeriodic implements PeriodicInterface
{
  /**
   * @var Redis
   */
  protected $redis;

  /**
   * @param Redis $redis
   * @return $this
   */
  public function setRedis(Redis $redis)
  {
    $this->redis = $redis;
    return $this;
  }

  /**
   * @return int (in second)
   */
  public function getTimeout()
  {
    return 10;
  }

  /**
   * Function excecuted n timeout.
   */
  public function tick()
  {
    //dump("Tick");
  }
}
