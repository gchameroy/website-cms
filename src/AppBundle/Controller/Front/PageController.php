<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/presentation", name="front_presentation")
     * @Method({"GET"})
     * @return Response
     */
    public function presentationAction() {
        return $this->render('front/page/presentation.html.twig');
    }

    /**
     * @Route("/partenaires", name="front_partners")
     * @Method({"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function partnersAction(EntityManagerInterface $entityManager) {
        $partners = $entityManager
            ->getRepository(Partner::class)
            ->findAll();

        return $this->render('front/page/partners.html.twig', [
            'partners' => $partners
        ]);
    }
}
