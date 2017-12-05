<?php
namespace AppBundle\Form\Type\User;

use AppBundle\Entity\User;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\Address\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('company', TextType::class)
            ->add('email', TextType::class)
            ->add('plainPassword', TextType::class)
            ->add('phone', TextType::class)
            ->add('offer', EntityType::class, [
                'class' => UserOffer::class,
                'choice_label' => function (UserOffer $offer) {
                    return $offer->getLabel();
                }
            ])
            ->add('billingAddress', AddressType::class)
            ->add('deliveryAddress', AddressType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
