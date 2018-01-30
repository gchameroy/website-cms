<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Gallery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends Controller
{
    /**
     * @Route("/gallerie/page-{page}", name="front_gallery")
     * @Method({"GET"})
     * @param int $page
     * @return Response
     */
    public function listAction(int $page) {
        if ($page <= 0) {
            return $this->redirectToRoute('front_gallery', [
                'page' => 1
            ]);
        }

        $nbPage = $this->getDoctrine()
            ->getRepository(Gallery::class)
            ->countNbPagePublished(11);

        if ($page > $nbPage) {
            return $this->redirectToRoute('front_gallery', [
                'page' => $nbPage
            ]);
        }

        $galleries = $this->getDoctrine()
            ->getRepository(Gallery::class)
            ->findAll(11, $page, 'desc');

        return $this->render('front/gallery/list.html.twig', [
            'galleries' => $galleries,
            'page' => $page,
            'nbPage' => $nbPage
        ]);

        return $this->render('front/gallery/list.html.twig', [
            'galleries' => $galleries
        ]);
    }
}
