<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\DeliveryZone;
use AppBundle\Form\Type\DeliveryZone\DeliveryZoneType;
use AppBundle\Manager\DeliveryZoneManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delivery-zones")
 */
class DeliveryZoneController extends Controller
{
    /**
     * @Route("/", name="admin_delivery_zones")
     * @Method({"GET"})
     * @param DeliveryZoneManager $deliveryZoneManager
     * @return Response
     */
    public function listAction(DeliveryZoneManager $deliveryZoneManager): Response
    {
        return $this->render('admin/delivery-zone/list.html.twig', [
            'deliveryZones' => $deliveryZoneManager->getList()
        ]);
    }

    /**
     * @Route("/add", name="admin_delivery_zones_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param DeliveryZoneManager $deliveryZoneManager
     * @return Response
     */
    public function addAction(Request $request, DeliveryZoneManager $deliveryZoneManager): Response
    {
        $deliveryZone = new DeliveryZone();

        $form = $this->createForm(DeliveryZoneType::class, $deliveryZone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryZoneManager->saveNew($deliveryZone);

            return $this->redirectToRoute('admin_delivery_zones');
        }

        return $this->render('admin/delivery-zone/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{deliveryZoneId}/edit", name="admin_delivery_zone_edit", requirements={"deliveryZoneId": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $deliveryZoneId
     * @param DeliveryZoneManager $deliveryZoneManager
     * @return Response
     */
    public function editAction(Request $request, int $deliveryZoneId, DeliveryZoneManager $deliveryZoneManager): Response
    {
        $deliveryZone = $deliveryZoneManager->get($deliveryZoneId);

        $form = $this->createForm(DeliveryZoneType::class, $deliveryZone);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryZoneManager->save($deliveryZone);

            return $this->redirectToRoute('admin_delivery_zones');
        }

        return $this->render('admin/delivery-zone/edit.html.twig', [
            'form' => $form->createView(),
            'deliveryZone' => $deliveryZone
        ]);
    }

    /**
     * @Route("/delete", name="admin_delivery_zone_delete")
     * @Method({"POST"})
     * @param Request $request
     * @param DeliveryZoneManager $deliveryZoneManager
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, DeliveryZoneManager $deliveryZoneManager): RedirectResponse
    {
        $deliveryZone = $deliveryZoneManager->get((int) $request->request->get('deliveryZone'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('delivery-zone-delete', $token)) {
            $deliveryZoneManager->remove($deliveryZone);
        }

        return $this->redirectToRoute('admin_delivery_zones');
    }
}
