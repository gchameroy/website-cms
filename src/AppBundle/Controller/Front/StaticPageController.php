<?php

namespace AppBundle\Controller\Front;

use AppBundle\Manager\StaticPageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticPageController extends Controller
{
    /** @var StaticPageManager */
    private $staticPageManager;

    public function __construct(StaticPageManager $staticPageManager)
    {
        $this->staticPageManager = $staticPageManager;
    }

    /**
     * @Route("/page/{slug}", name="front_static_page")
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug): Response
    {
        $staticPage = $this->staticPageManager->getPublishedBySlug($slug);

        return $this->render('front/static-page/view.html.twig', [
            'staticPage' => $staticPage
        ]);
    }
}
