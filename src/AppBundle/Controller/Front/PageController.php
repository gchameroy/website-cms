<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Manager\PartnerManager;

class PageController extends Controller
{
    /**
     * @Route("/presentation", name="front_presentation")
     * @Method({"GET"})
     * @return Response
     */
    public function presentationAction()
    {
        return $this->render('front/page/presentation.html.twig');
    }

    /**
     * @Route("/partenaires", name="front_partners")
     * @Method({"GET"})
     * @param $partnerManager PartnerManager
     * @return Response
     */
    public function partnersAction(PartnerManager $partnerManager)
    {
        $partners = $partnerManager->getList();

        return $this->render('front/page/partners.html.twig', [
            'partners' => $partners
        ]);
    }
}
