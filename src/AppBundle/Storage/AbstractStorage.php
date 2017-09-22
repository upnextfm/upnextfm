<?php
namespace AppBundle\Storage;

use Predis\Client as Redis;
use Psr\Log\LoggerInterface;

abstract class AbstractStorage
{
  /**
   * @var Redis
   */
    protected $redis;

  /**
   * @var LoggerInterface
   */
    protected $logger;

  /**
   * Constructor
   *
   * @param Redis $redis
   * @param LoggerInterface $logger
   */
    public function __construct(Redis $redis, LoggerInterface $logger)
    {
        $this->redis  = $redis;
        $this->logger = $logger;
    }
}
