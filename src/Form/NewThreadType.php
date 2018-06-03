<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('to', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'Enter username', 'autocomplete' => 'off'],
                'data' => $options['to']
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'input', 'placeholder' => 'Subject', 'autocomplete' => 'off'],
                'data' => $options['subject']
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['class' => 'textarea'],
            ])
            ->add('send', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'to' => '',
            'subject' => ''
        ));
    }
}
