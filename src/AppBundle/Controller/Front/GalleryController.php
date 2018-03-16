<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Gallery;
use AppBundle\Manager\GalleryManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GalleryController extends Controller
{
    /**
     * @Route("/galerie", name="front_gallery_home")
     * @Method({"GET"})
     * @return Response
     */
    public function homeAction()
    {
        return $this->redirectToRoute('front_gallery', [
            'page' => 1
        ]);
    }

    /**
     * @Route("/galerie/page-{page}", name="front_gallery")
     * @Method({"GET"})
     * @param int $page
     * @return Response
     */
    public function listAction(int $page = 1, GalleryManager $galleryManager)
    {
        if ($page <= 0) {
            return $this->redirectToRoute('front_gallery', [
                'page' => 1
            ]);
        }

        $nbPage = $galleryManager->getNbPages();

        if ($page > $nbPage) {
            return $this->redirectToRoute('front_gallery', [
                'page' => $nbPage
            ]);
        }

        $galleries = $galleryManager->getPublished($page);

        return $this->render('front/gallery/list.html.twig', [
            'galleries' => $galleries,
            'page' => $page,
            'nbPage' => $nbPage
        ]);
    }
}
