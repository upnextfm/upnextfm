<?php
namespace AppBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ContactModel
{
  /**
   * @var string
   * @Assert\NotBlank()
   */
    protected $name;

  /**
   * @var string
   * @Assert\NotBlank()
   * @Assert\Email()
   */
    protected $email;

  /**
   * @var string
   * @Assert\NotBlank()
   */
    protected $message;

  /**
   * @var string
   */
    protected $nonce;

  /**
   * @return string
   */
    public function getName()
    {
        return $this->name;
    }

  /**
   * @param string $name
   * @return ContactModel
   */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

  /**
   * @return string
   */
    public function getEmail()
    {
        return $this->email;
    }

  /**
   * @param string $email
   * @return ContactModel
   */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

  /**
   * @return string
   */
    public function getMessage()
    {
        return $this->message;
    }

  /**
   * @param string $message
   * @return ContactModel
   */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

  /**
   * @return string
   */
    public function getNonce()
    {
        return $this->nonce;
    }

  /**
   * @param string $nonce
   * @return ContactModel
   */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
        return $this;
    }
}
