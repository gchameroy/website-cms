<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\ProductSkill;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\Category\CategoryType;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Product\ProductPublishType;
use AppBundle\Form\Type\Product\ProductVariantType;
use AppBundle\Form\Type\ProductSkill\ProductSkillType;
use AppBundle\Form\Type\Product\ProductType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function listAction()
    {
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
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $offers = $em->getRepository(UserOffer::class)
            ->findAll();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);

            foreach ($offers as $offer) {
                $price = (new ProductPrice())
                    ->setProduct($product)
                    ->setOffer($offer)
                    ->setPrice($request->request->all()['product'][$offer->getFormName()]);
                $em->persist($price);
            }

            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('admin/product/add.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'offers' => $offers
        ]);
    }

    /**
     * /**
     * @Route("/publish", name="admin_product_publish")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Product $product */
        $product = $em->getRepository(Product::class)
            ->find($request->request->get('product'));
        $this->checkProduct($product);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-publish', $token)) {
            $product->setPublishedAt(new \DateTime());
            $em->persist($product);
            $em->flush();
        }

        $redirect = $this->generateUrl('admin_products');
        if ($request->request->get('redirect')) {
            $redirect = $request->request->get('redirect');
        }

        return $this->redirect($redirect);
    }

    /**
     * @Route("/unpublish", name="admin_product_unpublish")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Product $product */
        $product = $em->getRepository(Product::class)
            ->find($request->request->get('product'));
        $this->checkProduct($product);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-unpublish', $token)) {
            $product->setPublishedAt(null);
            $em->persist($product);
            $em->flush();
        }

        $redirect = $this->generateUrl('admin_products');
        if ($request->request->get('redirect')) {
            $redirect = $request->request->get('redirect');
        }

        return $this->redirect($redirect);
    }

    /**
     * @Route("/{id}/edit", name="admin_product_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        $offers = $entityManager
            ->getRepository(UserOffer::class)
            ->findAll();
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($product->getPrices() as $price) {
                $entityManager->remove($price);
            }
            $entityManager->flush();

            foreach ($offers as $offer) {
                $price = (new ProductPrice())
                    ->setProduct($product)
                    ->setOffer($offer)
                    ->setPrice($request->request->all()['product'][$offer->getFormName()]);
                $entityManager->persist($price);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $id,
            ]);
        }

        return $this->render('admin/product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id)
    {
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
    public function addImageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $variant = $em->getRepository(Product::class)
            ->findOneBy([
                'id' => $request->query->get('variant'),
                'parent' => $id
            ]);
        if (!$variant) {
            $variant = $em->getRepository(Product::class)
                ->findOneBy([
                    'id' => $request->query->get('variant'),
                    'parent' => null
                ]);
            $product = $variant;
        } else {
            $product = $variant->getParent();
        }

        $this->checkProduct($variant);
        $this->checkProduct($product);

        $image = $variant->getImage();
        if ($image) {
            $em = $this->getDoctrine()->getManager();
            $variant->setImage(null);
            $em->persist($variant);
            $em->remove($image);
            $em->flush();
        }

        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/product/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);
            $variant->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->persist($variant);
            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $id
            ]);
        }

        return $this->render('admin/product/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'variant' => $variant,
            'product' => $product
        ]);
    }

    /**
     * @Route("/add_category", name="admin_add_category", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addCategoryAction(Request $request)
    {
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
                    'label' => $category->getLabel()
                ]);
            }
        }

        return $this->render("admin/product/modal/add_category.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/add_skill", name="admin_product_skill_add", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addSkillAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $skill = new ProductSkill();
        $product = $em->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $form = $this->createForm(ProductSkillType::class, $skill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $skill->setProduct($product);
            $em->persist($skill);

            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/add_skill.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/edit_skill", name="admin_product_skill_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editSkillAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $skill = $em->getRepository(ProductSkill::class)
            ->findOneBy([
                'id' => $request->query->get('skill'),
                'product' => $id
            ]);
        $this->checkSkill($skill);

        $form = $this->createForm(ProductSkillType::class, $skill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($skill);

            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $skill->getProduct()->getId()
            ]);
        }

        return $this->render('admin/product/modal/edit_skill.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill
        ]);
    }

    /**
     * @Route("/{product}/delete", name="admin_product_delete", requirements={"product": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $product
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteAction(int $product, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)
            ->find($product);
        $this->checkProduct($product);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-delete', $token)) {
            $em->remove($product);
            $em->flush();
        }

        return $this->redirectToRoute('admin_products');
    }

    /**
     * @Route("/{product}/delete_skill", name="admin_product_skill_delete", requirements={"product": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $product
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSkillAction(int $product, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $skill = $em->getRepository(ProductSkill::class)
            ->findOneBy([
                'id' => $request->request->get('skill'),
                'product' => $product
            ]);
        $this->checkSkill($skill);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-skill-delete', $token)) {
            $em->remove($skill);
            $em->flush();
        }

        return $this->redirectToRoute('admin_product', [
            'id' => $skill->getProduct()->getId()
        ]);
    }

    /**
     * @param ProductSkill|null $skill
     */
    private function checkSkill(?ProductSkill $skill)
    {
        if (!$skill) {
            throw $this->createNotFoundException('Skill Not Found.');
        }
    }

    /**
     * @param Product|null $product
     */
    private function checkProduct(?Product $product)
    {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }

    /**
     * @Route("/{id}/add_variant", name="admin_product_variant_add", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addVariantAction(int $id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $variant = (new Product())
            ->setVariantName('');

        $product = $em->getRepository(Product::class)
            ->find($id);
        $this->checkProduct($product);

        $offers = $em->getRepository(UserOffer::class)
            ->findAll();

        $form = $this->createForm(ProductVariantType::class, $variant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $variant->setParent($product);
            $em->persist($variant);

            foreach ($offers as $offer) {
                $price = (new ProductPrice())
                    ->setProduct($variant)
                    ->setOffer($offer)
                    ->setPrice($request->request->all()['product'][$offer->getFormName()]);
                $em->persist($price);
            }

            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/add_variant.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/{id}/edit_variant", name="admin_product_variant_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editVariantAction(int $id, Request $request, EntityManagerInterface $entityManager)
    {
        $variant = $entityManager
            ->getRepository(Product::class)
            ->findOneBy([
                'id' => $request->query->get('variant'),
                'parent' => $id
            ]);
        if (!$variant) {
            $variant = $entityManager
                ->getRepository(Product::class)
                ->findOneBy([
                    'id' => $request->query->get('variant'),
                    'parent' => null
                ]);
            $product = $variant;
        } else {
            $product = $variant->getParent();
        }
        $this->checkProduct($variant);
        $this->checkProduct($product);

        $offers = $entityManager
            ->getRepository(UserOffer::class)
            ->findAll();

        $form = $this->createForm(ProductVariantType::class, $variant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($variant->getPrices() as $price) {
                $entityManager->remove($price);
            }
            $entityManager->flush();

            $entityManager->persist($variant);

            foreach ($offers as $offer) {
                $price = (new ProductPrice())
                    ->setProduct($variant)
                    ->setOffer($offer)
                    ->setPrice($request->request->all()['product'][$offer->getFormName()]);
                $entityManager->persist($price);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/edit_variant.html.twig', [
            'form' => $form->createView(),
            'variant' => $variant,
            'product' => $product,
            'offers' => $offers
        ]);
    }
}
