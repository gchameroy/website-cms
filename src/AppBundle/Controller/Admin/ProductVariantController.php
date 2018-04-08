<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Product\ProductVariantType;
use AppBundle\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/products")
 */
class ProductVariantController extends Controller
{
    /** @var ProductManager */
    private $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * @Route("/{id}/add_image", name="admin_product_add_image", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function addImageAction(Request $request, int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Product $variant */
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
     * @Route("/{id}/add_variant", name="admin_product_variant_add", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addVariantAction(int $id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $variant = (new Product())
            ->setVariantName('');

        $product = $this->productManager->get($id);

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
    public function editVariantAction(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var Product $variant */
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

            /** @var UserOffer $offer */
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

    private function checkProduct(?Product $product): void
    {
        if (!$product) {
            throw new NotFoundHttpException('Product Variant Not Found.');
        }
    }
}
