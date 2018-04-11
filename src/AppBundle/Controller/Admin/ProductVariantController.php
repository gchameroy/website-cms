<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Product\ProductVariantType;
use AppBundle\Manager\ImageManager;
use AppBundle\Manager\ProductManager;
use AppBundle\Manager\ProductVariantManager;
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
class ProductVariantController extends Controller
{
    /** @var ProductManager */
    private $productManager;

    /** @var ProductVariantManager */
    private $productVariantManager;

    /** @var ImageManager */
    private $imageManager;

    public function __construct(ProductManager $productManager, ProductVariantManager $productVariantManager, ImageManager $imageManager)
    {
        $this->productManager = $productManager;
        $this->productVariantManager = $productVariantManager;
        $this->imageManager = $imageManager;
    }

    /**
     * @Route("/{id}/add-image", name="admin_product_add_image", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param integer $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addImageAction(int $id, Request $request): Response
    {
        $product = $this->productManager->get($id);
        $variant = $this->productVariantManager->get($request->query->get('variant'));

        $image = $this->imageManager->getNew();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $variant->getImage();
            if ($image) {
                $variant->setImage(null);
                $this->productVariantManager->save($variant);
                $this->imageManager->remove($image);
            }

            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/product/';
            $file->move($filePath, $fileName);

            $image->setPath($fileName);
            $this->imageManager->save($image);

            $variant->setImage($image);
            $this->productVariantManager->save($variant);

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'variant' => $variant,
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/add-variant", name="admin_product_variant_add", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addVariantAction(int $id, Request $request): Response
    {
        $product = $this->productManager->get($id);
        $variant = $this->productVariantManager->getNew($product);

        $em = $this->getDoctrine()->getManager();
        $offers = $em->getRepository(UserOffer::class)
            ->findAll();

        $form = $this->createForm(ProductVariantType::class, $variant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productVariantManager->save($variant);

            /** @var UserOffer $offer */
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
     * @Route("/{id}/edit-variant", name="admin_product_variant_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editVariantAction(int $id, Request $request): Response
    {
        $product = $this->productManager->get($id);
        $variant = $this->productVariantManager->get($request->query->get('variant'));

        $em = $this->getDoctrine()->getManager();
        $offers = $em
            ->getRepository(UserOffer::class)
            ->findAll();

        $form = $this->createForm(ProductVariantType::class, $variant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productVariantManager->save($variant);
            $this->productVariantManager->removePrices($variant);

            /** @var UserOffer $offer */
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

        return $this->render('admin/product/modal/edit_variant.html.twig', [
            'form' => $form->createView(),
            'variant' => $variant,
            'product' => $product,
            'offers' => $offers
        ]);
    }
}
