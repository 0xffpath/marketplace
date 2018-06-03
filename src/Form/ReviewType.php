<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feedback', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Positive' => 'Positive',
                    'Neutral' => 'Neutral',
                    'Negative' => 'Negative',
                ],
                'label_attr' => ['class' => 'form-label'],
                'data' => $options['feedback']
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'textarea', 'rows' => 10, 'placeholder' => 'Please leave feedback for the product.'],
                'data' => $options['comment']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'feedback' => '',
            'comment' => '',
        ));
    }
}
