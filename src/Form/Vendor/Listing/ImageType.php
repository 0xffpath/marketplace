<?php
namespace App\Form\Vendor\Listing;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', FileType::class, [
                'attr' => ['class' => 'input', 'style' => 'height:100%;', 'accept' => '.png, .jpg, .jpeg'],
                'required' => $options['fileRequired'],
            ])
            ->add('upload', SubmitType::class, [
                'attr' => ['class' => 'button is-link'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'fileRequired' => true,
        ));
    }
}
