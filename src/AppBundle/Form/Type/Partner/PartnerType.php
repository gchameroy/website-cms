<?php
namespace AppBundle\Form\Type\Partner;

use AppBundle\Entity\Partner;
use AppBundle\Form\Type\Address\AddressPartnerType;
use AppBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', AddressPartnerType::class)
            ->add('image', ImageType::class)
            ->add('website', UrlType::class, ['required' => false])
            ->add('phone', TextType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Partner::class,
        ]);
    }
}
