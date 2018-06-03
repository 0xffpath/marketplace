<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('physical', CheckboxType::class, [
                'required' => false,
                'data' => $options['physical']
            ])
            ->add('digital', CheckboxType::class, [
                'required' => false,
                'data' => $options['digital']
            ])
            ->add('min', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $options['min']
            ])
            ->add('max', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $options['max']
            ])
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
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'data' => $options['currency']
            ])
            ->add('level', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $options['level']
            ])
            ->add('feedback', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $options['feedback']
            ])
            ->add('trusted', CheckboxType::class, [
                'required' => false,
                'data' => $options['trusted']
            ])
            ->add('active', CheckboxType::class, [
                'required' => false,
                'data' => $options['active']
            ])
            ->add('apply', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'currency' => 'USD',
            'physical' => true,
            'digital' => true,
            'min' => '',
            'max' => '',
            'level' => 1,
            'feedback' => 0,
            'trusted' => false,
            'active' => false,
        ));
    }
}
