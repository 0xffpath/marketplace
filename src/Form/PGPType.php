<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PGPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pgp', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'rows' => '10', 'required' => 'true'],
                'data' => $options['pgp']
            ])
            ->add('tfa', CheckboxType::class, [
                'required' => false,
                'data' => $options['tfa']
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'pgp' => '',
            'tfa' => ''
        ));
    }
}
