<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BuyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'textarea',
                    'required' => 'true',
                    'rows' => 5,
                    'placeholder' => $options['placeholder']
                ],
            ])
            ->add('encrypt', CheckboxType::class, [
                'attr' => [],
                'required' => false
            ])
            ->add('coupon', TextType::class, [
                'attr' => [
                    'class' => 'input is-large',
                    'placeholder' => 'Coupon Code'
                ],
                'required' => false
            ])
            ->add('continue', SubmitType::class, [
                'attr' => [
                    'class' => 'button is-success is-large is-pulled-right',
                    'value' => 'Continue to Payment'
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'placeholder' => '',
        ));
    }
}
