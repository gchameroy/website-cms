<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Setting;
use Doctrine\Common\Persistence\ObjectManager;

class SettingFixtures extends FixtureHelper
{

    public function load(ObjectManager $manager)
    {
        $this->loadFirst($manager);
        $this->loadSecond($manager);
    }

    public function loadFirst(ObjectManager $manager)
    {
        $setting = (new setting())
                ->setPresentation('Ma belle presentation')
                ->setAdress($this->getReference('address-user-1'))
                ->setPhone('+33678451298')
                ->setTitleSeo('Titre SEO')
                ->setDescriptionSeo('Description SEO')
                ->setSocialNetwork('http://facedebook/#128');
                
        $this->setReference('mon_setting', $setting);
        $manager->persist($setting);

        $manager->flush();
    }

    public function loadSecond(ObjectManager $manager)
    {
        $setting = (new setting())
                ->setPresentation('Presentation commerciale qui déchire')
                ->setAdress($this->getReference('address-user-2'))
                ->setPhone('+33655555555')
                ->setTitleSeo('SEO quand tu nous tiens')
                ->setDescriptionSeo('SEO est super sympa et mignon, il faut vraiment essayer')
                ->setSocialNetwork('http://fessedebouc/#437');
                
        $this->setReference('the_setting', $setting);
        $manager->persist($setting);

        $manager->flush();
    }

}
