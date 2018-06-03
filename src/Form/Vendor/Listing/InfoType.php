<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'input', 'autocomplete' => 'off'],
                'data' => $options['title']
            ])
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => $options['parent'],
                'choice_attr' => $options['selected'],
            ])
            ->add('stock', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'leave blank for unlimited',
                    'min' => '1',
                    'max' => '999999',
                    'autocomplete' => 'off'
                ],
                'data' => $options['stock'],
            ])
            ->add('next', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => '',
            'price' => '',
            'sex' => '',
            'parent' => [],
            'selected' => [],
            'stock' => null,
        ));
    }
}
