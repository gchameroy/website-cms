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
     * @Route("/newsletters/{newsletter}", name="front_newsletter")
     * @Method({"GET"})
     * @param int $newsletter
     * @return Response
     */
    public function viewAction($newsletter) {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findOnePublished($newsletter);
        $this->checkNewsletter($newsletter);

        return $this->render('front/home/newsletter/view.html.twig', [
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
