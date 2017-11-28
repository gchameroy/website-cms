<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Entity\StaticPage;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\StaticPage\StaticPageDeleteType;
use AppBundle\Form\Type\StaticPage\StaticPagePublishType;
use AppBundle\Form\Type\StaticPage\StaticPageType;
use AppBundle\Form\Type\StaticPage\StaticPageUnpublishType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/static-pages")
 */
class StaticPageController extends Controller
{
    /**
     * @Route("/", name="admin_static_pages")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction() {
        $staticPages = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->findAll(0, null, 'desc', false);

        return $this->render('admin/static-page/list.html.twig', [
            'staticPages' => $staticPages
        ]);
    }

    /**
     * @Route("/add", name="admin_static_pages_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $staticPage = new StaticPage();

        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staticPage);
            $em->flush();

            $this->addFlash('success', 'Static page added successfully.');

            return $this->redirectToRoute('admin_static_page', [
                'id' => $staticPage->getId(),
            ]);
        }

        return $this->render('admin/static-page/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_static_page", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);

        return $this->render('admin/static-page/view.html.twig', [
            'staticPage' => $staticPage
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_static_page_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, $id) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);
        $staticPage->setPublishedAt(new \DateTime());

        $form = $this->createForm(StaticPagePublishType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staticPage);
            $em->flush();

            $this->addFlash('success', 'Static page published successfully.');

            return $this->redirectToRoute('admin_static_pages');
        }

        return $this->render('admin/static-page/publish.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/unpublish", name="admin_static_page_unpublish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, $id) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);

        if (null === $staticPage->getPublishedAt()) {
            return $this->redirectToRoute('admin_static_pages');
        }

        $form = $this->createForm(StaticPageUnpublishType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $staticPage->setPublishedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($staticPage);
            $em->flush();

            $this->addFlash('success', 'Static page unpublished successfully.');

            return $this->redirectToRoute('admin_static_pages');
        }

        return $this->render('admin/static-page/unpublish.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_static_page_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);

        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($staticPage);
            $em->flush();

            $this->addFlash('success', 'Static page edited successfully.');

            return $this->redirectToRoute('admin_static_page', [
                'id' => $staticPage->getId()
            ]);
        }

        return $this->render('admin/static-page/edit.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_static_page_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);

        $form = $this->createForm(StaticPageDeleteType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($staticPage);
            $em->flush();

            $this->addFlash('success', 'Static page deleted successfully.');

            return $this->redirectToRoute('admin_static_pages');
        }

        return $this->render('admin/static-page/delete.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/add_image", name="admin_static_page_add_image", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function addImageAction(Request $request, $id) {
        $staticPage = $this->getDoctrine()
            ->getRepository(StaticPage::class)
            ->find($id);
        $this->checkStaticPage($staticPage);

        $image = $staticPage->getImage();
        if ($image) {
            $em = $this->getDoctrine()->getManager();
            $staticPage->removeImage();
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
            $filePath = $this->get('kernel')->getRootDir().'/../uploads/static-page/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);
            $staticPage->setImage($image);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $this->addFlash('success_image', 'Image added successfully.');

            return $this->redirectToRoute('admin_static_page', [
                'id' => $id
            ]);
        }

        return $this->render('admin/static-page/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }

    /**
     * @param $staticPage
     */
    private function checkStaticPage($staticPage) {
        if (!$staticPage) {
            throw $this->createNotFoundException('Static page Not Found.');
        }
    }
}
