<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RegisterType extends AbstractType
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
            ->add('role', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'buyer' => 'buyer',
                    'new vendor' => 'new_vendor',
                ],
                'attr' => ['class' => 'input is-medium'],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'input is-medium',
                        'placeholder' => 'password'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm',
                    'attr' => [
                        'class' => 'input is-medium',
                        'placeholder' => 'confirm password'
                    ],
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
