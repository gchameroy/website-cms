<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\PointOfSale;
use AppBundle\Form\Type\PointOfSale\PointOfSaleType;
use AppBundle\Service\GoogleMaps;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/point-of-sale")
 */
class PointOfSaleController extends Controller
{
    /**
     * @Route("/", name="admin_point_of_sales")
     * @Method({"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function listAction(EntityManagerInterface $entityManager)
    {
        $pointOfSales = $entityManager
            ->getRepository(PointOfSale::class)
            ->findAll();

        return $this->render('admin/point-of-sale/list.html.twig', [
            'pointOfSales' => $pointOfSales
        ]);
    }

    /**
     * @Route("/add", name="admin_point_of_sales_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param GoogleMaps $gmaps
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, EntityManagerInterface $entityManager, GoogleMaps $gmaps)
    {
        $pointOfSale = new PointOfSale();

        $form = $this->createForm(PointOfSaleType::class, $pointOfSale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $gmaps->geoLocateAddress($pointOfSale->getAddress()->getFormattedAddress());
            if ($location) {
                $pointOfSale->getAddress()->setLat($location->getLat());
                $pointOfSale->getAddress()->setLng($location->getLng());
            }

            $entityManager->persist($pointOfSale);
            $entityManager->flush();

            return $this->redirectToRoute('admin_point_of_sales');
        }

        return $this->render('admin/point-of-sale/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{pointOfSale}/edit", name="admin_point_of_sale_edit", requirements={"pointOfSale": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $pointOfSale
     * @param EntityManagerInterface $entityManager
     * @param GoogleMaps $gmaps
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $pointOfSale, EntityManagerInterface $entityManager, GoogleMaps $gmaps)
    {
        /** @var PointOfSale $pointOfSale */
        $pointOfSale = $entityManager
            ->getRepository(PointOfSale::class)
            ->find($pointOfSale);
        $this->checkPointOfSale($pointOfSale);

        $form = $this->createForm(PointOfSaleType::class, $pointOfSale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $gmaps->geoLocateAddress($pointOfSale->getAddress()->getFormattedAddress());
            if ($location) {
                $pointOfSale->getAddress()->setLat($location->getLat());
                $pointOfSale->getAddress()->setLng($location->getLng());
            }

            $entityManager->persist($pointOfSale);
            $entityManager->flush();

            return $this->redirectToRoute('admin_point_of_sales');
        }

        return $this->render('admin/point-of-sale/edit.html.twig', [
            'form' => $form->createView(),
            'pointOfSale' => $pointOfSale
        ]);
    }

    /**
     * @Route("/delete", name="admin_point_of_sale_delete")
     * @Method({"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request, EntityManagerInterface $entityManager)
    {
        $pointOfSale = $entityManager
            ->getRepository(PointOfSale::class)
            ->find($request->request->get('pointOfSale'));
        $this->checkPointOfSale($pointOfSale);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('point-of-sale-delete', $token)) {
            $entityManager->remove($pointOfSale);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_point_of_sales');
    }

    /**
     * @param PointOfSale|null $pointOfSale
     */
    private function checkPointOfSale(?PointOfSale $pointOfSale)
    {
        if (!$pointOfSale) {
            throw $this->createNotFoundException('Point Of Sale Not Found.');
        }
    }
}
