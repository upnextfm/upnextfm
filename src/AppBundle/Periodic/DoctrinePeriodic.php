<?php
namespace AppBundle\Periodic;

use Doctrine\ORM\EntityManagerInterface;
use Gos\Bundle\WebSocketBundle\Periodic\PeriodicInterface;
use Psr\Log\LoggerInterface;

/**
 * Periodically pings the database to keep the connection open.
 */
class DoctrinePeriodic implements PeriodicInterface
{
  /**
   * @var EntityManagerInterface
   */
  protected $em;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * @param LoggerInterface $logger
   * @return $this
   */
  public function setLogger(LoggerInterface $logger)
  {
    $this->logger = $logger;
    return $this;
  }

  /**
   * @param EntityManagerInterface $em
   * @return $this
   */
  public function setEntityManager(EntityManagerInterface $em)
  {
    $this->em = $em;
    return $this;
  }

  /**
   * @return int (in second)
   */
  public function getTimeout()
  {
    return 30;
  }

  /**
   * Function executed n timeout.
   */
  public function tick()
  {
    $this->logger->debug("DoctrinePeriodic: Pinging database.");
    if ($this->em->getConnection()->ping() === false) {
      $this->logger->warning("DoctrinePeriodic: Reconnecting to database.");
      $this->em->getConnection()->close();
      $this->em->getConnection()->connect();
    }
  }
}
