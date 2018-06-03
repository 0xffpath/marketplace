<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => ['class' => 'input', 'autocomplete' => 'off', 'required' => 'true'],
                'data' => $options['title']
            ])
            ->add('image', FileType::class, [
                'attr' => ['class' => 'input', 'style' => 'height:100%;', 'accept' => '.png, .jpg, .jpeg'],
                'required' => $options['fileRequired'],
            ])
            ->add('price', TextType::class, [
                'attr' => ['class' => 'input', 'autocomplete' => 'off', 'required' => 'true'],
                'data' => $options['price']
            ])
            ->add('type', ChoiceType::class, [
                'attr' => ['required' => 'true'],
                'choices' => [
                    'physical' => 'physical',
                    'digital' => 'digital',
                ],
                'data' => $options['type']
            ])
            ->add('category', ChoiceType::class, [
                'attr' => ['required' => 'true'],
                'choices' => $options['categories'],
                'data' => $options['category']
            ])
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
            ->add('btc', CheckboxType::class, [
                'required' => false,
                'data' => $options['btc']
            ])
            ->add('xmr', CheckboxType::class, [
                'required' => false,
                'data' => $options['xmr']
            ])
            ->add('zec', CheckboxType::class, [
                'required' => false,
                'data' => $options['zec']
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'textarea', 'autocomplete' => 'off', 'rows' => '10', 'required' => 'true'],
                'data' => $options['description']
            ])
            ->add('sex', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'male' => 'male',
                    'female' => 'female',
                    'uni' => 'uni',
                ],
                'data' => $options['sex']
            ])
            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => $options['parent'],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'button is-success'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title' => '',
            'price' => '',
            'fileRequired' => false,
            'type' => '',
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
            'btc' => false,
            'xmr' => false,
            'zec' => false,
            'description' => '',
            'category' => '',
            'categories' => [],
            'image' => '',
            'sex' => '',
            'parent' => [],
        ));
    }
}
