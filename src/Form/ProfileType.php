<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, [
                'attr' => ['accept' => '.png, .jpg, .jpeg', 'class' => 'input', 'style' => 'height:100%;'],
                'required' => $options['fileRequired'],
            ])
            ->add('profile', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'rows' => '10', 'required' => 'true', 'placeholder' => 'Put general information here. This will show up on your profile.'],
                'data' => $options['profile']
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'fileRequired' => false,
            'profile' => '',
        ));
    }
}
