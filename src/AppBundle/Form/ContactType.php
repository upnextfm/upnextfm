<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array $options
   */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("name", TextType::class, [
        "required" => true,
        "label"    => "Name",
        "attr"     => [
          "class" => "validate"
        ]
        ])
        ->add("email", EmailType::class, [
        "required" => true,
        "label"    => "Email",
        "attr"     => [
          "class" => "validate"
        ]
        ])
        ->add("message", TextareaType::class, [
        "required" => true,
        "label"    => "Message",
        "attr"     => [
          "class" => "materialize-textarea validate",
          "rows"  => 10
        ]
        ])
        ->add("nonce", HiddenType::class, [
        "attr" => ["class" => "up-nonce"]
        ])
        ->add("submit", SubmitType::class)
        ;
    }
}
