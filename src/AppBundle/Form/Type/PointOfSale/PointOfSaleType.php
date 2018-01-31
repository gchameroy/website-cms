<?php
namespace AppBundle\Form\Type\PointOfSale;

use AppBundle\Entity\PointOfSale;
use AppBundle\Form\Type\Address\AddressForPointOfSaleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointOfSaleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressForPointOfSaleType::class)
            ->add('website', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PointOfSale::class,
        ]);
    }
}
