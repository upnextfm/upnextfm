<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
  public function getParent()
  {
    return RegistrationFormType::class;
  }

  public function getBlockPrefix()
  {
    return "registration";
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      "csrf_protection" => false,
    ]);
  }
}
