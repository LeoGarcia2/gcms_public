<?php

namespace App\Form;

use Vich\UploaderBundle\Form\Type\VichFileType;

use App\Entity\CTAaa;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CTAaaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author')
            ->add('displayName')
            ->add('updatedAt')
            ->add('published')
            ->add('aze')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CTAaa::class,
            'allow_extra_fields' => true
        ]);
    }
}
