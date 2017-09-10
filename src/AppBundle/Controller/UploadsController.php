<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Photo;
use AppBundle\Form\PhotoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class UploadsController Extends Controller
{
     /**
     * @Route("/uploads/products/{product_id}/image/{photo_id}", name="photo_view", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $product_id
     * @param integer $photo_id
     * @return BinaryFileResponse
     */
    public function viewProductPhotoAction($product_id, $photo_id) {
        $photo = $this->getDoctrine()->getRepository(Photo::class)->find($photo_id);
        $filePath = $this->get('kernel')->getRootDir().'/../uploads/product/';
        $file = $filePath.$photo->getPath();
        return new BinaryFileResponse($file);
    }

    /**
     * @param $photo
     */
    private function checkPhoto($photo) {
        if (!$photo) {
            $this->createNotFoundException('Photo Not Found.');
        }
    }
}
