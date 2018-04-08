<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\Product\ProductType;
use AppBundle\Manager\ProductManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/products")
 */
class ProductController extends Controller
{
    /** @var ProductManager */
    private $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * @Route("/", name="admin_products")
     * @return Response
     */
    public function listAction(): Response
    {
        $products = $this->productManager->getList();

        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction(int $id): Response
    {
        $product = $this->productManager->get($id);

        return $this->render('admin/product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/add", name="admin_products_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $offers = $em->getRepository(UserOffer::class)
            ->findAll();

        $product = $this->productManager->getNew();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productManager->save($product);

            /** @var UserOffer $offer */
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
     * @Route("/{id}/edit", name="admin_product_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $product = $this->productManager->get($id);
        $offers = $entityManager
            ->getRepository(UserOffer::class)
            ->findAll();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($product->getPrices() as $price) {
                $entityManager->remove($price);
            }
            $entityManager->flush();

            /** @var UserOffer $offer */
            foreach ($offers as $offer) {
                $price = (new ProductPrice())
                    ->setProduct($product)
                    ->setOffer($offer)
                    ->setPrice($request->request->all()['product'][$offer->getFormName()]);
                $entityManager->persist($price);
            }
            $entityManager->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('admin/product/edit.html.twig', [
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
    public function publishAction(Request $request): Response
    {
        $product = $this->productManager->get($request->request->get('product'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-publish', $token)) {
            $product->setPublishedAt(new \DateTime());
            $this->productManager->save($product);
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
    public function unpublishAction(Request $request): Response
    {
        $product = $this->productManager->get($request->request->get('product'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-unpublish', $token)) {
            $product->setPublishedAt(null);
            $this->productManager->save($product);
        }

        $redirect = $this->generateUrl('admin_products');
        if ($request->request->get('redirect')) {
            $redirect = $request->request->get('redirect');
        }

        return $this->redirect($redirect);
    }
}
