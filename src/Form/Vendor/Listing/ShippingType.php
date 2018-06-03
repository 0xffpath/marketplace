<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipping', ChoiceType::class, [
                'attr' => ['required' => 'true'],
                'multiple' => 'true',
                'choices' => $options['shippingOptions'],
                'choice_attr' => $options['selectedShipping'],
                'data' => $options['shipping']
            ])
            ->add('southAmerica', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['southAmericaOptions'],
                'choice_attr' => $options['southAmericaSelected'],
                'data' => $options['southAmerica'],
                'required' => false,
            ])
            ->add('asia', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['asiaOptions'],
                'choice_attr' => $options['asiaSelected'],
                'data' => $options['asia'],
                'required' => false,
            ])
            ->add('africa', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['africaOptions'],
                'choice_attr' => $options['africaSelected'],
                'data' => $options['africa'],
                'required' => false,
            ])
            ->add('australia', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['australiaOptions'],
                'choice_attr' => $options['australiaSelected'],
                'data' => $options['australia'],
                'required' => false,
            ])
            ->add('europe', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['europeOptions'],
                'choice_attr' => $options['europeSelected'],
                'data' => $options['europe'],
                'required' => false,
            ])
            ->add('northAmerica', ChoiceType::class, [
                'multiple' => 'true',
                'choices' => $options['northAmericaOptions'],
                'choice_attr' => $options['northAmericaSelected'],
                'data' => $options['northAmerica'],
                'required' => false,
            ])
            ->add('southAmericaAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('asiaAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('africaAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('australiaAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('europeAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('northAmericaAll', CheckboxType::class, [
                'required' => false,
            ])
            ->add('from', ChoiceType::class, [
                'attr' => ['required' => 'true'],
                'choices' => $options['fromOptions'],
                'data' => $options['from']
            ])
            ->add('next', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'southAmericaSelected' => [],
            'asiaSelected' => [],
            'africaSelected' => [],
            'australiaSelected' => [],
            'europeSelected' => [],
            'northAmericaSelected' => [],
            'selectedShipping' => [],
            'shipping' => [],
            'shippingOptions' => [],
            'southAmerica' => [],
            'southAmericaOptions' => [],
            'asia' => [],
            'asiaOptions' => [],
            'africa' => [],
            'africaOptions' => [],
            'australia' => [],
            'australiaOptions' => [],
            'europe' => [],
            'europeOptions' => [],
            'northAmerica' => [],
            'northAmericaOptions' => [],
            'from' => '',
            'fromOptions' => [],
        ));
    }
}
