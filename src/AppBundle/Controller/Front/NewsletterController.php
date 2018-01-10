<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Newsletter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends Controller
{
    /**
     * @Route("/actualites/{slug}", name="front_newsletter")
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug) {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findOnePublished($slug);
        $this->checkNewsletter($newsletter);

        return $this->render('front/newsletter/newsletter.html.twig', [
            'newsletter' => $newsletter
        ]);
    }

    /**
     * @param $newsletter
     */
    private function checkNewsletter($newsletter) {
        if (!$newsletter) {
            throw $this->createNotFoundException('Newsletter Not Found.');
        }
    }
}
