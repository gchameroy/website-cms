<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Newsletter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends Controller
{
    /**
     * @Route("/actualites", name="front_newsletters_home")
     * @Method({"GET"})
     * @return RedirectResponse
     */
    public function homeAction()
    {
        return $this->redirectToRoute('front_newsletters', [
            'page' => 1
        ]);
    }

    /**
     * @Route("/actualites/page-{page}", name="front_newsletters")
     * @Method({"GET"})
     * @param int $page
     * @return Response
     */
    public function listAction(int $page)
    {
        if ($page <= 0) {
            return $this->redirectToRoute('front_newsletters', [
                'page' => 1
            ]);
        }

        $nbPage = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->countNbPagePublished();

        if ($page > $nbPage) {
            return $this->redirectToRoute('front_newsletters', [
                'page' => $nbPage
            ]);
        }

        $newsletters = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findPublishedByPage($page);

        return $this->render('front/newsletter/list.html.twig', [
            'newsletters' => $newsletters,
            'page' => $page,
            'nbPage' => $nbPage
        ]);
    }

    /**
     * @Route("/actualites/{slug}", name="front_newsletter", requirements={"slug"=".+"})
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug)
    {
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
    private function checkNewsletter($newsletter)
    {
        if (!$newsletter) {
            throw $this->createNotFoundException('Newsletter Not Found.');
        }
    }
}
