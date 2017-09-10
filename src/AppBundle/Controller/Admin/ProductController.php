<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Product;
use AppBundle\Form\PhotoType;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/products")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="admin_products")
     * @return Response
     */
    public function listAction() {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

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

            $this->addFlash('success', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('admin_product_view', [
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
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $this->checkProduct($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');

            return $this->redirectToRoute('admin_product_view', [
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
     * @Method({"GET"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request, $id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $this->checkProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_products');
    }

    /**
     * @Route("/{id}", name="admin_product_view", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $this->checkProduct($product);

        return $this->render('admin/product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/add_photo", name="admin_product_add_photo", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function addPhotoAction(Request $request, $id) {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        $photo = new Photo();
        $photo->setProduct($product);

        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $path */
            $path = $photo->getPath();
            $file = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir().'/../uploads/product/';
            $path->move($filePath, $file);
            $photo->setPath($file);

            $em = $this->getDoctrine()->getManager();
            $em->persist($photo);
            $em->flush();

            $this->addFlash('success_photo', 'Photo ajoutée avec succès.');

            return $this->redirectToRoute('admin_product_view', [
                'id' => $id
            ]);
        }

        return $this->render('admin/product/modal/add_photo.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            $this->createNotFoundException('Product Not Found.');
        }
    }
}
