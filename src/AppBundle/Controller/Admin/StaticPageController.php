<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\StaticPage\StaticPageDeleteType;
use AppBundle\Form\Type\StaticPage\StaticPagePublishType;
use AppBundle\Form\Type\StaticPage\StaticPageType;
use AppBundle\Form\Type\StaticPage\StaticPageUnpublishType;
use AppBundle\Manager\StaticPageManager;
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
    /** @var StaticPageManager */
    private $staticPageManager;

    public function __construct(StaticPageManager $staticPageManager)
    {
        $this->staticPageManager = $staticPageManager;
    }

    /**
     * @Route("/", name="admin_static_pages")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction(): Response
    {
        $staticPages = $this->staticPageManager->getList();

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
    public function addAction(Request $request): Response
    {
        $staticPage = $this->staticPageManager->getNew();
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $staticPage = $this->staticPageManager->save($staticPage);

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
    public function viewAction(int $id): Response
    {
        $staticPage = $this->staticPageManager->get($id);

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
    public function publishAction(Request $request, int $id): Response
    {
        $staticPage = $this->staticPageManager->get($id);
        $staticPage->setPublishedAt(new \DateTime());
        $form = $this->createForm(StaticPagePublishType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->staticPageManager->save($staticPage);

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
    public function unpublishAction(Request $request, int $id): Response
    {
        $staticPage = $this->staticPageManager->get($id);
        if (null === $staticPage->getPublishedAt()) {
            return $this->redirectToRoute('admin_static_pages');
        }
        $form = $this->createForm(StaticPageUnpublishType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $staticPage->setPublishedAt(null);
            $this->staticPageManager->save($staticPage);

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
    public function editAction(Request $request, int $id): Response
    {
        $staticPage = $this->staticPageManager->get($id);
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $staticPage = $this->staticPageManager->save($staticPage);

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
    public function deleteAction($id, Request $request): Response
    {
        $staticPage = $this->staticPageManager->get($id);
        $form = $this->createForm(StaticPageDeleteType::class, $staticPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->staticPageManager->remove($staticPage);

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
    public function addImageAction(Request $request, int $id): Response
    {
        $staticPage = $this->staticPageManager->get($id);
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/static-page/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);
            $staticPage->setImage($image);
            $staticPage = $this->staticPageManager->save($staticPage);

            return $this->redirectToRoute('admin_static_page', [
                'id' => $staticPage->getId()
            ]);
        }

        return $this->render('admin/static-page/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'staticPage' => $staticPage,
        ]);
    }
}
