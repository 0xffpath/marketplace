<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ModerateListingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'rows' => '2', 'required' => 'true'],
            ])
            ->add('listing', HiddenType::class, [
                'data' => $options['listing']
            ])
            ->add('accept', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'listing' => ''
        ));
    }
}
