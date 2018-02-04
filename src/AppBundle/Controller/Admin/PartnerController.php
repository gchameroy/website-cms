<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Partner;
use AppBundle\Form\Type\Partner\PartnerEditType;
use AppBundle\Form\Type\Partner\PartnerType;
use Doctrine\ORM\EntityManagerInterface;
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
    /**
     * @Route("/", name="admin_partners")
     * @Method({"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function listAction(EntityManagerInterface $entityManager)
    {
        $partners = $entityManager
            ->getRepository(Partner::class)
            ->findAll();

        return $this->render('admin/partner/list.html.twig', [
            'partners' => $partners
        ]);
    }

    /**
     * @Route("/add", name="admin_partners_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, EntityManagerInterface $entityManager)
    {
        $partner = new Partner();

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

            $entityManager->persist($partner);
            $entityManager->flush();

            return $this->redirectToRoute('admin_partners');
        }

        return $this->render('admin/partner/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{partner}/edit", name="admin_partner_edit", requirements={"partner": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $partner
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $partner, EntityManagerInterface $entityManager)
    {
        $partner = $entityManager
            ->getRepository(Partner::class)
            ->find($partner);
        $this->checkPartner($partner);

        $form = $this->createForm(PartnerEditType::class, $partner);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partner);
            $entityManager->flush();

            return $this->redirectToRoute('admin_partners');
        }

        return $this->render('admin/partner/edit.html.twig', [
            'form' => $form->createView(),
            'partner' => $partner
        ]);
    }

    /**
     * @Route("/delete", name="admin_partner_delete")
     * @Method({"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request, EntityManagerInterface $entityManager)
    {
        $partner = $entityManager
            ->getRepository(Partner::class)
            ->find($request->request->get('partner'));
        $this->checkPartner($partner);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('partner-delete', $token)) {
            $entityManager->remove($partner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_partners');
    }

    /**
     * @param Partner|null $partner
     */
    private function checkPartner(?Partner $partner)
    {
        if (!$partner) {
            throw $this->createNotFoundException('Partner Not Found.');
        }
    }
}
