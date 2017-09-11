<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @Route("/")
 */
class ImageController Extends Controller
{
     /**
     * @Route(
      *     "/images/p{product_id}-{image_id}", name="product_image_view",
      *     requirements={"product_id": "\d+", "image_id": "\d+"}
      * )
     * @Method({"GET"})
     * @param integer $product_id
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewProductImageAction($product_id, $image_id) {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->findOneBy([
                'id' => $image_id,
                'product' => $product_id
            ]);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/product/';
        $file = $filePath.$image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @Route(
     *     "/images/n{newsletter_id}-{image_id}", name="newsletter_image_view",
     *     requirements={"newsletter_id": "\d+", "image_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param integer $newsletter_id
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewNewsletterImageAction($newsletter_id, $image_id) {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->findOneBy([
                'id' => $image_id,
                'newsletter' => $newsletter_id
            ]);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/newsletter/';
        $file = $filePath.$image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @param $image
     */
    private function checkImage($image) {
        if (!$image) {
            $this->createNotFoundException('Image Not Found.');
        }
    }
}
