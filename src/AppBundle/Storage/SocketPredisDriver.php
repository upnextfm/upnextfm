<?php
namespace AppBundle\Storage;

use Gos\Bundle\WebSocketBundle\Client\Driver\DriverInterface;
use Predis\Client as Redis;

class SocketPredisDriver implements DriverInterface
{
  /**
   * @var Redis
   */
    protected $client;

  /**
   * string $prefix
   */
    protected $prefix;

  /**
   * @param Redis $client
   * @param string $prefix
   */
    public function __construct(Redis $client, $prefix = '')
    {
        $this->client = $client;
        $this->prefix = ($prefix !== false ? $prefix . ':' : '');
    }

  /**
   * {@inheritdoc}
   */
    public function fetch($id)
    {
        $result = $this->client->get($this->prefix . $id);
        if (null === $result) {
            return false;
        }

        return $result;
    }

  /**
   * {@inheritdoc}
   */
    public function contains($id)
    {
        return $this->client->exists($this->prefix . $id);
    }

  /**
   * {@inheritdoc}
   */
    public function save($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            $response = $this->client->setex($this->prefix . $id, $lifeTime, $data);
        } else {
            $response = $this->client->set($this->prefix . $id, $data);
        }

        return $response === true || $response == 'OK';
    }

  /**
   * @param string $id
   * @param int $lifeTime
   * @return bool
   */
    public function lifeTime($id, $lifeTime)
    {
        $response = $this->client->expire($this->prefix . $id, $lifeTime);
        return $response === true || $response == 'OK';
    }

  /**
   * {@inheritdoc}
   */
    public function delete($id)
    {
        return $this->client->del($this->prefix . $id) > 0;
    }
}
