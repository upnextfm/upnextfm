<?php
namespace AppBundle\Api;

/**
 * Stores an API response.
 */
class Response
{
  /**
   * @var mixed
   */
    private $data;

  /**
   * @var int
   */
    private $statusCode = 200;

  /**
   * @var array
   */
    private $headers = [];

  /**
   * Constructor
   *
   * @param mixed $data
   * @param int $statusCode
   * @param array $headers
   */
    public function __construct($data, $statusCode = 200, $headers = [])
    {
        $this->setData($data);
        $this->setStatusCode($statusCode);
        $this->setHeaders($headers);
    }

  /**
   * @return mixed
   */
    public function getData()
    {
        return $this->data;
    }

  /**
   * @param mixed $data
   * @return $this
   */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

  /**
   * @return int
   */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

  /**
   * @param int $statusCode
   * @return $this
   */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

  /**
   * @return array
   */
    public function getHeaders()
    {
        return $this->headers;
    }

  /**
   * @param array $headers
   * @return $this
   */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }
}
