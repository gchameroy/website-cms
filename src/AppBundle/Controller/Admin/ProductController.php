<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryAttribute;
use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\Category\CategoryType;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Product\ProductPublishType;
use AppBundle\Form\Type\Product\ProductDeleteType;
use AppBundle\Form\Type\Product\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $options = [
            'perPage' => 0,
            'order' => 'desc',
            'published' => false
        ];
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll($options);

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
        $categoriesAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->findAll();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $attributes = $request->request->get('attributes');
            foreach ($attributes As $attribute_id) {
                $attribute = $this->getDoctrine()->getRepository(Attribute::class)->find($attribute_id);
                $product->addAttribute($attribute);
            }

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
            'categories_attribute' => $categoriesAttribute
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_product_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, $id) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductPublishType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setPublishedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product published successfully.');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/publish.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/unpublish", name="admin_product_unpublish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, $id) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        if (null === $product->getPublishedAt()) {
            return $this->redirectToRoute('admin_products');
        }

        $form = $this->createForm(ProductPublishType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setPublishedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product unpublished successfully.');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/unpublish.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
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
        $categoriesAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->findAll();
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->removeAttributes();
            $attributes = $request->request->get('attributes');
            foreach ($attributes As $attribute_id) {
                $attribute = $this->getDoctrine()->getRepository(Attribute::class)->find($attribute_id);
                $product->addAttribute($attribute);
            }

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
            'categories_attribute' => $categoriesAttribute
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

            $this->addFlash('success', 'Product deleted successfully.');

            return $this->redirectToRoute('admin_products', [
                'id' => $id,
            ]);
        }

        return $this->render('admin/product/delete.html.twig', [
            'form' => $form->createView(),
            'product' => $product
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

        $categoriesAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->findAll();


        return $this->render('admin/product/view.html.twig', [
            'product' => $product,
            'categories_attribute' => $categoriesAttribute
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
     * @Route("/add_category", name="admin_add_category", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addCategoryAction(Request $request) {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $category = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($category);
                $em->flush();

                return new JsonResponse([
                    'success' => true,
                    'id' => $category->getId(),
                    'label'  => $category->getLabel()
                ]);
            }
        }

        return $this->render("admin/product/modal/add_category.html.twig", array(
            'form'  =>  $form->createView()
        ));
    }

    /**
     * @param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }

    /**
     * @param $categoryAttribute
     */
    private function checkCategoryAttribute($categoryAttribute) {
        if (!$categoryAttribute) {
            throw $this->createNotFoundException('Category Attribute Not Found.');
        }
    }
}
