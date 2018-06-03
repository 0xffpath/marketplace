<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'choose category',
                ],
                'choices' => $options['categories'],
                'data' => $options['category'],
                'choice_attr' => [
                    '' => [
                        'disabled' => true,
                        'style' => 'display:none;',
                    ],
                    'select' => [
                        'disabled' => true,
                        'style' => 'display:none;',
                    ],
                ],
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'physical' => 'physical',
                    'digital' => 'digital',
                ],
                'data' => $options['type']
            ])
            ->add('next', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'category' => '',
            'categories' => [],
            'type' => '',
        ));
    }
}
