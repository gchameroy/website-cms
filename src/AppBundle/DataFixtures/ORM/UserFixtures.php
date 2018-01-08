<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $this->loadAdmin($manager);
        $this->loadUsers($manager);
        $this->loadProfessionals($manager);
    }

    private function loadAdmin(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@test.fr')
            ->setPlainPassword('admin')
            ->setPhone($this->faker->phoneNumber)
            ->setFirstName($this->faker->firstName())
            ->setLastName($this->faker->lastName)
            ->setCompany($this->faker->company)
            ->setIsAdmin(true)
            ->setOffer($this->getReference('user-offer-none'))
            ->setBillingAddress($this->getReference('address-admin'))
            ->setDeliveryAddress($this->getReference('address-admin'));

        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password)
            ->eraseCredentials();

        $this->setReference('user-admin', $user);
        $manager->persist($user);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        for ($i = 1; $i <= 15; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email)
                ->setPlainPassword('user')
                ->setPassword($this->faker->password())
                ->setPhone($this->faker->phoneNumber)
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName)
                ->setPlainPassword($this->faker->password)
                ->setOffer($this->getReference('user-offer-none'))
                ->setBillingAddress($this->getReference('address-user-' . $i))
                ->setDeliveryAddress($this->getReference('address-user-' . $i));

            if (!isset($password)) {
                $password = $this->container->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword());
            }
            $user->setPassword($password)
                ->eraseCredentials();

            $this->setReference('user-' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function loadProfessionals(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail($this->faker->companyEmail)
                ->setPassword('pro')
                ->setPhone($this->faker->phoneNumber)
                ->setFirstName($this->faker->firstName())
                ->setLastName($this->faker->lastName)
                ->setCompany($this->faker->company)
                ->setOffer($this->getReference('user-offer-pro'))
                ->setBillingAddress($this->getReference('address-pro-' . $i))
                ->setDeliveryAddress($this->getReference('address-pro-' . $i));

            if (!isset($password)) {
                $password = $this->container->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword());
            }
            $user->setPassword($password)
                ->eraseCredentials();

            $this->setReference('user-pro-' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserOfferFixtures::class,
            AddressFixtures::class
        ];
    }
}
