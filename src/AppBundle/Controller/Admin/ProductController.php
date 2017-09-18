<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\ProductDeleteType;
use AppBundle\Form\Type\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/products")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="admin_products")
     * @return Response
     */
    public function listAction() {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/add", name="admin_products_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product added successfully.');

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('admin/product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_product_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product edited successfully.');

            return $this->redirectToRoute('admin_product', [
                'id' => $id,
            ]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_product_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductDeleteType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();

            $this->addFlash('success', 'Newsletter deleted successfully.');

            return $this->redirectToRoute('admin_products', [
                'id' => $id,
            ]);
        }

        return $this->render('admin/product/delete.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        return $this->render('admin/product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/add_image", name="admin_product_add_image", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function addImageAction(Request $request, $id) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $image = new Image();
        $image->setProduct($product);

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir().'/../uploads/product/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $this->addFlash('success_image', 'Image added successfully.');

            return $this->redirectToRoute('admin_product', [
                'id' => $id
            ]);
        }

        return $this->render('admin/product/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }
}
