<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('admin@test.fr')
            ->setPlainPassword('admin');
        $password = $this->container->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password)
            ->eraseCredentials();
        $user->setAdmin(true);

        $this->setReference('user-admin', $user);
        $manager->persist($user);

        $manager->flush();
    }
}
