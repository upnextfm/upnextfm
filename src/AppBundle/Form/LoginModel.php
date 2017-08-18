<?php
namespace AppBundle\Form;

class LoginModel
{
  /**
   * @var string
   */
  protected $username;

  /**
   * @var string
   */
  protected $password;

  /**
   * @var bool
   */
  protected $remember;

  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @param string $username
   * @return $this
   */
  public function setUsername($username)
  {
    $this->username = $username;
    return $this;
  }

  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @param string $password
   * @return $this
   */
  public function setPassword($password)
  {
    $this->password = $password;
    return $this;
  }

  /**
   * @return boolean
   */
  public function isRemember()
  {
    return $this->remember;
  }

  /**
   * @param boolean $remember
   * @return $this
   */
  public function setRemember($remember)
  {
    $this->remember = $remember;
    return $this;
  }
}
