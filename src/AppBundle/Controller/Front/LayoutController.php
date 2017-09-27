<?php

namespace AppBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @return Response
     */
    public function menuAction() {
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        return $this->render('front/layout/menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @param int $max
     * @return Response
     */
    public function newslettersAction($max = 2) {
        $newsletters = $this->getDoctrine()
            ->getRepository('AppBundle:Newsletter')
            ->findAll($max, 1, 'desc');

        return $this->render('front/home/partial/newsletters.html.twig', [
            'newsletters' => $newsletters
        ]);
    }
}
