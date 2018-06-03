<?php
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new', PasswordType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'handle password', 'autocomplete' => 'off'],
                'required' => false,
            ])
            ->add('confirm', PasswordType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'confirm', 'autocomplete' => 'off'],
                'required' => false,
            ])
            ->add('old', PasswordType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'current password', 'autocomplete' => 'off'],
                'required' => false,
            ])
            ->add('pin', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'pin', 'autocomplete' => 'off'],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
        ));
    }
}
