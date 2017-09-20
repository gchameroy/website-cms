<?php

namespace AppBundle\Form\Type\Attribute;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\CategoryAttribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class)
            ->add('categoryAttribute', EntityType::class, array(
                'class' => CategoryAttribute::class,
                'choice_label' => function (CategoryAttribute $categoryAttribute) {
                    return $categoryAttribute->getLabel();
                },
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class
        ]);
    }
}
