<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\StaticPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticPageController extends Controller
{
    /**
     * @Route("/page/{slug}", name="front_static_page")
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findOnePublished($slug);
        $this->checkStaticPage($staticPage);

        return $this->render('front/static-page/view.html.twig', [
            'staticPage' => $staticPage
        ]);
    }

    /**
     * @param StaticPage|null $staticPage
     */
    private function checkStaticPage(?StaticPage $staticPage) {
        if (!$staticPage) {
            throw $this->createNotFoundException('Static Page Not Found.');
        }
    }
}
