<?php
namespace AppBundle\Form\Type\Product;

use AppBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('label')
            ->remove('description')
            ->remove('more1')
            ->remove('more2')
            ->remove('more3')
            ->remove('titleSEO')
            ->remove('descriptionSEO')
            ->remove('category')
            ->add('variantName', TextType::class)
        ;
    }

    public function getParent()
    {
        return ProductType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
