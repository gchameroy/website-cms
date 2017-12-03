<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use Doctrine\Common\Persistence\ObjectManager;

class NewsletterFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_NEWSLETTER; $i++) {
            $newsletter = new Newsletter();
            $newsletter->setTitle('Titre de l\'actualité')
                ->setContent($this->faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setImage($this->loadImage($manager));

            $this->setReference('newsletter-' . $i, $newsletter);
            $manager->persist($newsletter);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $uploadPath =  __DIR__ . '/../../../../uploads/newsletter';
        $fixturesPath =  __DIR__ . '/../img/newsletter';

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }
}
