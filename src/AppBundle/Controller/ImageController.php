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
     *     "/images/p-{image_id}", name="product_image_view",
     *     requirements={"image_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewProductImageAction($image_id)
    {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($image_id);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/product/';

        $file = $filePath . 'img_no_product.jpg';
        if ($image) {
            $file = $filePath . $image->getPath();
        }

        return new BinaryFileResponse($file);
    }

    /**
     * @Route(
     *     "/images/n-{image_id}", name="newsletter_image_view",
     *     requirements={"image_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewNewsletterImageAction($image_id)
    {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($image_id);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/newsletter/';
        $file = $filePath . $image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @Route(
     *     "/images/sp-{image_id}", name="static_page_image_view",
     *     requirements={"image_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewStaticPageImageAction($image_id)
    {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($image_id);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/static-page/';
        $file = $filePath . $image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @Route(
     *     "/images/g-{image_id}", name="gallery_image_view",
     *     requirements={"image_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param integer $image_id
     * @return BinaryFileResponse
     */
    public function viewGalleryImageAction($image_id)
    {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($image_id);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/gallery/';
        $file = $filePath . $image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @param $image
     */
    private function checkImage($image)
    {
        if (!$image) {
            $this->createNotFoundException('Image Not Found.');
        }
    }
}
