<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CurrencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currency', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'AUD' => 'AUD',
                    'CAD' => 'CAD',
                    'CHF' => 'CHF',
                    'EUR' => 'EUR',
                    'GBP' => 'GBP',
                    'NZD' => 'NZD',
                    'USD' => 'USD',
                ],
                'data' => $options['currency']
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'currency' => 'USD',
        ));
    }
}
