<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/product/';
        if ($image) {
            $file = $filePath.$image->getPath();
        } else {
            $file = $filePath.'img_no_product.png';
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
    public function viewNewsletterImageAction($image_id) {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($image_id);
        $this->checkImage($image);

        $filePath = $this->get('kernel')->getRootDir() . '/../uploads/newsletter/';
        $file = $filePath.$image->getPath();

        return new BinaryFileResponse($file);
    }

    /**
     * @Route(
     *     "/image/p-{product_id}/{id}/delete", name="delete_product_image",
     *     requirements={"id": "\d+","product_id": "\d+"}
     * )
     * @Method({"GET"})
     * @param int $id
     * @param int $product_id
     * @return Response
     */
    public function deleteImage($id, $product_id) {
        $image = $this->getDoctrine()
            ->getRepository(Image::class)
            ->find($id);
        $this->checkImage($image);

        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return $this->redirectToRoute('admin_product', [
            'id' => $product_id
        ]);
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
     * @param $image
     */
    private function checkImage($image) {
        if (!$image) {
            $this->createNotFoundException('Image Not Found.');
        }
    }
}
