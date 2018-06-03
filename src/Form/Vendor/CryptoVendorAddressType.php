<?php
namespace App\Form\Vendor;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CryptoVendorAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bitcoin', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'Bitcoin HD public master', 'autocomplete' => 'off'],
                'required' => false,
                'data' => $options['bitcoin'],
            ])
            ->add('monero', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'Monero address', 'autocomplete' => 'off'],
                'required' => false,
                'data' => $options['monero']
            ])
            ->add('zcash', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'Zcash address', 'autocomplete' => 'off'],
                'required' => false,
                'data' => $options['zcash']
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
            'bitcoin' => '',
            'monero' => '',
            'zcash' => '',
        ));
    }
}
