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

class PartnerEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('image')
        ;
    }

    public function getParent()
    {
        return PartnerType::class;
    }
}
