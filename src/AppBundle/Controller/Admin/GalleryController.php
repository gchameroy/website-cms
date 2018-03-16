<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Gallery;
use AppBundle\Form\Type\Gallery\GalleryType;
use AppBundle\Form\Type\Gallery\GalleryEditType;
use AppBundle\Manager\GalleryManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/gallery")
 */
class GalleryController extends Controller
{
    /**
     * @Route("/", name="admin_galleries")
     * @Method({"GET"})
     * @param GalleryManager $galleryManager
     * @return Response
     */
    public function listAction(GalleryManager $galleryManager)
    {
        $galleries = $galleryManager->getList();

        return $this->render('admin/gallery/list.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/add", name="admin_galleries_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param GalleryManager $galleryManager
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, GalleryManager $galleryManager)
    {
        $gallery = new Gallery();

        $form = $this->createForm(GalleryType::class, $gallery);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $gallery->getImage()->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/gallery/';
            $file->move($filePath, $fileName);
            $gallery->getImage()->setPath($fileName);

            $galleryManager->save($gallery);

            return $this->redirectToRoute('admin_galleries');
        }

        return $this->render('admin/gallery/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/publish", name="admin_gallery_publish", requirements={"id": "\d+"})
     * @Method({"POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, GalleryManager $galleryManager)
    {
        $gallery = $galleryManager->get($request->request->get('gallery'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-publish', $token)) {
            $gallery->setPublishedAt(new \DateTime());

            $galleryManager->save($gallery);
        }

        return $this->redirectToRoute('admin_galleries');
    }

    /**
     * @Route("/unpublish", name="admin_gallery_unpublish", requirements={"id": "\d+"})
     * @Method({"POST"})
     * @param Request $request
     * @param GalleryManager $galleryManager
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, GalleryManager $galleryManager)
    {
        $gallery = $galleryManager->get($request->request->get('gallery'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-unpublish', $token)) {
            $gallery->setPublishedAt(null);

            $galleryManager->save($gallery);
        }

        return $this->redirectToRoute('admin_galleries');
    }

    /**
     * @Route("/{gallery}/edit", name="admin_gallery_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param int $gallery
     * @param GalleryManager $galleryManager
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $gallery, GalleryManager $galleryManager)
    {
        $gallery = $galleryManager->get($gallery);

        $form = $this->createForm(GalleryEditType::class, $gallery);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $galleryManager->save($gallery);

            return $this->redirectToRoute('admin_galleries');
        }

        return $this->render('admin/gallery/edit.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery
        ]);
    }

    /**
     * @Route("/delete", name="admin_gallery_delete", requirements={"id": "\d+"})
     * @Method({"POST"})
     * @param Request $request
     * @param GalleryManager $galleryManager
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request, GalleryManager $galleryManager)
    {

        $gallery = $galleryManager->get($request->request->get('gallery'));

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-delete', $token)) {
            $galleryManager->remove($gallery);
        }

        return $this->redirectToRoute('admin_galleries');
    }
}
