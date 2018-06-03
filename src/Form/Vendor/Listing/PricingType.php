<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PricingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', TextType::class, [
                'attr' => ['class' => 'input', 'autocomplete' => 'off'],
                'data' => $options['price']
            ])
            ->add('btc', CheckboxType::class, [
                'required' => false,
                'data' => $options['btc']
            ])
            ->add('xmr', CheckboxType::class, [
                'required' => false,
                'data' => $options['xmr']
            ])
            ->add('zec', CheckboxType::class, [
                'required' => false,
                'data' => $options['zec']
            ])
            ->add('next', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'price' => '',
            'btc' => false,
            'xmr' => false,
            'zec' => false,
        ));
    }
}
