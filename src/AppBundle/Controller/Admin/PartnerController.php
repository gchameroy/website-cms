<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\Partner\PartnerEditType;
use AppBundle\Form\Type\Partner\PartnerType;
use AppBundle\Manager\PartnerManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/partners")
 */
class PartnerController extends Controller
{
    /** @var PartnerManager */
    private $partnerManager;

    public function __construct(PartnerManager $partnerManager)
    {
        $this->partnerManager = $partnerManager;
    }

    /**
     * @Route("/", name="admin_partners")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction()
    {
        $partners = $this->partnerManager->getList();

        return $this->render('admin/partner/list.html.twig', [
            'partners' => $partners
        ]);
    }

    /**
     * @Route("/add", name="admin_partners_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request): Response
    {
        $partner = $this->partnerManager->getNew();

        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $partner->getImage()->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = sprintf(
                '%s/../uploads/partner',
                $this->get('kernel')->getRootDir()
            );

            $file->move($filePath, $fileName);
            $partner->getImage()->setPath($fileName);

            $this->partnerManager->save($partner);

            return $this->redirectToRoute('admin_partners');
        }

        return $this->render('admin/partner/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{partner}/edit", name="admin_partner_edit", requirements={"partner": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $partner
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(int $partner, Request $request): Response
    {
        $partner = $this->partnerManager->get($partner);

        $form = $this->createForm(PartnerEditType::class, $partner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->partnerManager->save($partner);

            return $this->redirectToRoute('admin_partners');
        }

        return $this->render('admin/partner/edit.html.twig', [
            'form' => $form->createView(),
            'partner' => $partner,
        ]);
    }

    /**
     * @Route("/delete", name="admin_partner_delete")
     * @Method({"POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request): Response
    {
        $partner = $this->partnerManager->get($request->request->get('partner'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('partner-delete', $token)) {
            $this->partnerManager->remove($partner);
        }

        return $this->redirectToRoute('admin_partners');
    }
}
