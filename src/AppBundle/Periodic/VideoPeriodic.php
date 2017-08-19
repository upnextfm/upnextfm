<?php
namespace AppBundle\Periodic;

use Gos\Bundle\WebSocketBundle\Periodic\PeriodicInterface;

class VideoPeriodic implements PeriodicInterface
{
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
