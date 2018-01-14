<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends Controller
{
    /**
     * @Route("/privacy-policy", name="front_privacy")
     * @Method({"GET"})
     * @return Response
     */
    public function privacyAction() {
        return $this->render('front/page/privacy.html.twig');
    }

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
     * @return Response
     */
    public function partnersAction() {
        return $this->render('front/page/partners.html.twig');
    }
}
