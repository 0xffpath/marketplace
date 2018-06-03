<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InstallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'input is-medium',
                    'placeholder' => 'username',
                    'autocomplete' => 'off'
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'class' => 'input is-medium',
                    'placeholder' => 'password'
                ],
            ])
            ->add('pin', IntegerType::class, [
                'attr' => [
                    'class' => 'input is-medium',
                    'placeholder' => 'pin',
                    'min' => '100',
                    'max' => '999999',
                    'autocomplete' => 'off'
                ],
            ])
            ->add('pgp', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'rows' => '10', 'required' => 'true'],
            ])
            ->add('tfa', CheckboxType::class, [
                'required' => false,
            ])
            ->add('install', SubmitType::class, [
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
