<?php
namespace AppBundle\Form\Type\Gallery;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GalleryEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('image')
        ;
    }

    public function getParent()
    {
        return GalleryType::class;
    }
}
