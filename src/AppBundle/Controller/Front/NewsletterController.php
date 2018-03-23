<?php

namespace AppBundle\Controller\Front;

use AppBundle\Manager\NewsletterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends Controller
{
    /** @var NewsletterManager */
    private $newsletterManager;

    public function __construct(NewsletterManager $newsletterManager)
    {
        $this->newsletterManager = $newsletterManager;
    }

    /**
     * @Route("/actualites/page-{page}", name="front_newsletters")
     * @Method({"GET"})
     * @param int $page
     * @return Response
     */
    public function listAction(int $page = 1) {
        if ($page <= 0) {
            return $this->redirectToRoute('front_newsletters', [
                'page' => 1
            ]);
        }

        $nbPage = $this->newsletterManager->getNbPagePublished();

        if ($page > $nbPage) {
            return $this->redirectToRoute('front_newsletters', [
                'page' => $nbPage
            ]);
        }

        $newsletters = $this->newsletterManager->getListPublishedByPage($page);

        return $this->render('front/newsletter/list.html.twig', [
            'newsletters' => $newsletters,
            'page' => $page,
            'nbPage' => $nbPage
        ]);
    }

    /**
     * @Route("/actualites/{slug}", name="front_newsletter")
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug) {
        $newsletter = $this->newsletterManager->getPublishedBySlug($slug);

        return $this->render('front/newsletter/newsletter.html.twig', [
            'newsletter' => $newsletter
        ]);
    }
}
