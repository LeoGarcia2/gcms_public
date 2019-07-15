<?php

namespace App\Form;

use Vich\UploaderBundle\Form\Type\VichFileType;

use App\Entity\CTAzaza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CTAzazaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author')
            ->add('displayName')
            ->add('updatedAt')
            ->add('published')
            ->add('arez')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CTAzaza::class,
            'allow_extra_fields' => true
        ]);
    }
}
