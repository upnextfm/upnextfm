<?php
namespace AppBundle\Entity;

class ValueDecorator
{
  /**
   * @var object
   */
  protected $entity;

  /**
   * @var array
   */
  protected $values = [];

  /**
   * @param object $entity
   * @param array  $values
   */
  public function __construct($entity, array $values = [])
  {
    $this->entity = $entity;
    $this->values = $values;
  }

  /**
   * @param string $key
   * @param mixed  $value
   */
  public function set($key, $value)
  {
    $this->values[$key] = $value;
  }

  /**
   * @param string $key
   * @param mixed  $value
   */
  public function __set($key, $value)
  {
    $this->set($key, $value);
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function get($key)
  {
    return $this->values[$key];
  }

  /**
   * @param string $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->get($key);
  }

  /**
   * @param string $method
   * @param array $args
   * @return mixed
   */
  public function __call($method, $args)
  {
    if (isset($this->values[$method])) {
      return $this->values[$method];
    }
    $method = sprintf("get%s", ucwords($method));
    return call_user_func_array([$this->entity, $method], $args);
  }
}
