<?php
namespace AppBundle\EventListener\Socket;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractListener
{
  /**
   * @var EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * @var EntityManagerInterface
   */
  protected $em;

  /**
   * Constructor
   *
   * @param EventDispatcherInterface $eventDispatcher
   * @param EntityManagerInterface $em
   * @param LoggerInterface $logger
   */
  public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em, LoggerInterface $logger)
  {
    $this->eventDispatcher = $eventDispatcher;
    $this->em              = $em;
    $this->logger          = $logger;
  }
}
