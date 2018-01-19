<?php

namespace AppBundle\Form\Type\Cart;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class)
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => function ($product) use ($options) {
                    $price = $product->getPrice($options['data']['offer']);
                    return sprintf(
                        '%s (%s€)',
                        $product->getVariantName(),
                        number_format($price, 2, '.', ' ')
                    );
                },
                'query_builder' => function (ProductRepository $repository) use ($options) {
                    return $repository->findPublishedVariants($options['data']['product']);
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
