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
}
