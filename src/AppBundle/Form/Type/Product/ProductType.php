<?php

namespace AppBundle\Form\Type\Product;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\UserOffer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextType::class)
            ->add('reference', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('more1', TextType::class, ['required' => false])
            ->add('more2', TextType::class, ['required' => false])
            ->add('more3', TextType::class, ['required' => false])
            ->add('titleSEO', TextType::class, [
                    'required' => false
            ])
            ->add('descriptionSEO', TextType::class, [
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getLabel();
                }
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $offers = $this->em->getRepository(UserOffer::class)
            ->findAll();

        /** @var UserOffer $offer */
        foreach ($offers as $offer) {
            $form->add('product_price_' . $offer->getId(), NumberType::class, [
                'label' => $offer->getLabel(),
                'scale' => 2,
                'mapped' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}
