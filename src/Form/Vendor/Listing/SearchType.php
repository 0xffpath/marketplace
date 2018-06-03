<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keywords', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'autocomplete' => 'off', 'rows' => '10', 'required' => 'true'],
                'data' => $options['keywords']
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'keywords' => '',
        ));
    }
}
