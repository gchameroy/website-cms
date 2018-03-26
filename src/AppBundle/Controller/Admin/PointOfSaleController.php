<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\PointOfSale\PointOfSaleType;
use AppBundle\Manager\PointOfSaleManager;
use AppBundle\Service\GoogleMaps;
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
    /** @var PointOfSaleManager */
    private $pointOfSaleManager;

    public function __construct(PointOfSaleManager $pointOfSaleManager)
    {
        $this->pointOfSaleManager = $pointOfSaleManager;
    }

    /**
     * @Route("/", name="admin_point_of_sales")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction(): Response
    {
        $pointOfSales = $this->pointOfSaleManager->getList();

        return $this->render('admin/point-of-sale/list.html.twig', [
            'pointOfSales' => $pointOfSales
        ]);
    }

    /**
     * @Route("/add", name="admin_point_of_sales_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param GoogleMaps $gmaps
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, GoogleMaps $gmaps): Response
    {
        $pointOfSale = $this->pointOfSaleManager->getNew();

        $form = $this->createForm(PointOfSaleType::class, $pointOfSale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $gmaps->geoLocateAddress($pointOfSale->getAddress()->getFormattedAddress());
            if ($location) {
                $pointOfSale->getAddress()->setLat($location->getLat());
                $pointOfSale->getAddress()->setLng($location->getLng());
            }

            $this->pointOfSaleManager->save($pointOfSale);

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
     * @param GoogleMaps $gmaps
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $pointOfSale, GoogleMaps $gmaps): Response
    {
        $pointOfSale = $this->pointOfSaleManager->get($pointOfSale);

        $form = $this->createForm(PointOfSaleType::class, $pointOfSale);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $gmaps->geoLocateAddress($pointOfSale->getAddress()->getFormattedAddress());
            if ($location) {
                $pointOfSale->getAddress()->setLat($location->getLat());
                $pointOfSale->getAddress()->setLng($location->getLng());
            }

            $this->pointOfSaleManager->save($pointOfSale);

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
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request): Response
    {
        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('point-of-sale-delete', $token)) {
            $pointOfSale = $this->pointOfSaleManager->get($request->request->get('pointOfSale'));
            $this->pointOfSaleManager->remove($pointOfSale);
        }

        return $this->redirectToRoute('admin_point_of_sales');
    }
}
