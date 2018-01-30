<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Gallery;
use AppBundle\Form\Type\Gallery\GalleryType;
use AppBundle\Form\Type\Gallery\GalleryEditType;
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
     * @return Response
     */
    public function listAction()
    {
        $galleries = $this->getDoctrine()
            ->getRepository(Gallery::class)
            ->findAll(0, null, 'desc', false);

        return $this->render('admin/gallery/list.html.twig', [
            'galleries' => $galleries
        ]);
    }

    /**
     * @Route("/add", name="admin_galleries_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
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

            $em = $this->getDoctrine()->getManager();
            $em->persist($gallery);
            $em->flush();

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
    public function publishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)
            ->find($request->request->get('gallery'));
        $this->checkGallery($gallery);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-publish', $token)) {
            $gallery->setPublishedAt(new \DateTime());
            $em->persist($gallery);
            $em->flush();
        }

        return $this->redirectToRoute('admin_galleries');
    }

    /**
     * @Route("/unpublish", name="admin_gallery_unpublish", requirements={"id": "\d+"})
     * @Method({"POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)
            ->find($request->request->get('gallery'));
        $this->checkGallery($gallery);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-unpublish', $token)) {
            $gallery->setPublishedAt(null);
            $em->persist($gallery);
            $em->flush();
        }

        return $this->redirectToRoute('admin_galleries');
    }

    /**
     * @Route("/{gallery}/edit", name="admin_gallery_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $gallery
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $gallery)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)
            ->find($gallery);
        $this->checkGallery($gallery);

        $form = $this->createForm(GalleryEditType::class, $gallery);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($gallery);
            $em->flush();

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
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)
            ->find($request->request->get('gallery'));
        $this->checkGallery($gallery);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('gallery-delete', $token)) {
            $em->remove($gallery);
            $em->flush();
        }

        return $this->redirectToRoute('admin_galleries');
    }

    /**
     * @param $gallery|null
     */
    private function checkGallery(?Gallery $gallery)
    {
        if (!$gallery) {
            throw $this->createNotFoundException('Newsletter Not Found.');
        }
    }
}
