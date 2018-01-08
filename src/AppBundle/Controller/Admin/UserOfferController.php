<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\UserOffer\UserOfferDeleteType;
use AppBundle\Form\Type\UserOffer\UserOfferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user-offers")
 */
class UserOfferController extends Controller
{
    /**
     * @Route("/", name="admin_user_offers")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction() {
        $offers = $this->getDoctrine()
            ->getRepository(UserOffer::class)
            ->findAll();

        return $this->render('admin/user-offer/list.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/add", name="admin_user_offers_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $offer = new UserOffer();

        $form = $this->createForm(UserOfferType::class, $offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);

            $products = $em->getRepository(Product::class)
                ->findAll();
            /** @var Product $product */
            foreach ($products as $product) {
                $price = (new ProductPrice())
                    ->setOffer($offer)
                    ->setProduct($product)
                    ->setPrice($product->getDefaultPrice());
                $em->persist($price);
            }

            $em->flush();

            $this->addFlash('success', 'Offer added successfully.');

            return $this->redirectToRoute('admin_user_offers');
        }

        return $this->render('admin/user-offer/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_offer_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $offer = $this->getDoctrine()
            ->getRepository(UserOffer::class)
            ->find($id);
        $this->checkOffer($offer);

        $form = $this->createForm(UserOfferType::class, $offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);
            $em->flush();

            $this->addFlash('success', 'Offer edited successfully.');

            return $this->redirectToRoute('admin_user_offers');
        }

        return $this->render('admin/user-offer/edit.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_user_offer_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $offer = $this->getDoctrine()
            ->getRepository(UserOffer::class)
            ->find($id);
        $this->checkOffer($offer);

        $form = $this->createForm(UserOfferDeleteType::class, $offer);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($offer);
            $em->flush();

            $this->addFlash('success', 'Offer deleted successfully.');

            return $this->redirectToRoute('admin_user_offers');
        }

        return $this->render('admin/user-offer/delete.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }

    /**
     * @param UserOffer|null $offer
     */
    private function checkOffer(?UserOffer $offer) {
        if (!$offer) {
            throw $this->createNotFoundException('Offer Not Found.');
        }

        if ($offer->getLabel() === 'Sans offre') {
            throw $this->createAccessDeniedException('This offer cannot be modified.');
        }
    }
}
