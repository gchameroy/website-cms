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
                ->setAdresse('Ici et la')
                ->setTelephone('+33678451298')
                ->setTitreSeo('Titre SEO')
                ->setDescriptionSeo('Description SEO')
                ->setPhotoPres('..\..photo45.jpg')
                ->setReseauxSociaux('http://facedebook/#128');
                
        $this->setReference('mon_setting', $setting);
        $manager->persist($setting);

        $manager->flush();
    }

    public function loadSecond(ObjectManager $manager)
    {
        $setting = (new setting())
                ->setPresentation('Presentation commerciale qui déchire')
                ->setAdresse('Dans le coin pas loin')
                ->setTelephone('+33655555555')
                ->setTitreSeo('SEO quand tu nous tiens')
                ->setDescriptionSeo('SEO est super sympa et mignon, il faut vraiment essayer')
                ->setPhotoPres('..\..photo38.jpg')
                ->setReseauxSociaux('http://fessedebouc/#437');
                
        $this->setReference('the_setting', $setting);
        $manager->persist($setting);

        $manager->flush();
    }

}
