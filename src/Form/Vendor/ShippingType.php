<?php
namespace App\Form\Vendor;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'option', 'autocomplete' => 'off'],
            ])
            ->add('price', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'price',
                    'autocomplete' => 'off'
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('add', SubmitType::class, [
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
