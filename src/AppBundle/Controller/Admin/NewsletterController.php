<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Newsletter\NewsletterDeleteType;
use AppBundle\Form\Type\Newsletter\NewsletterPublishType;
use AppBundle\Form\Type\Newsletter\NewsletterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/newsletters")
 */
class NewsletterController extends Controller
{
    /**
     * @Route("/", name="admin_newsletters")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction()
    {
        $newsletters = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findAll(0, null, 'desc', false);

        return $this->render('admin/newsletter/list.html.twig', [
            'newsletters' => $newsletters
        ]);
    }

    /**
     * @Route("/add", name="admin_newsletters_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request)
    {
        $newsletter = new Newsletter();

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            $this->addFlash('success', 'Newsletter added successfully.');

            return $this->redirectToRoute('admin_newsletter', [
                'id' => $newsletter->getId(),
            ]);
        }

        return $this->render('admin/newsletter/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_newsletter", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        return $this->render('admin/newsletter/view.html.twig', [
            'newsletter' => $newsletter
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_newsletter_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, $id)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        $form = $this->createForm(NewsletterPublishType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setPublishedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            $this->addFlash('success', 'Newsletter published successfully.');

            return $this->redirectToRoute('admin_newsletters');
        }

        return $this->render('admin/newsletter/publish.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/{id}/unpublish", name="admin_newsletter_unpublish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, $id)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        if (null === $newsletter->getPublishedAt()) {
            return $this->redirectToRoute('admin_newsletters');
        }

        $form = $this->createForm(NewsletterPublishType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setPublishedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            $this->addFlash('success', 'Newsletter unpublished successfully.');

            return $this->redirectToRoute('admin_newsletters');
        }

        return $this->render('admin/newsletter/unpublish.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_newsletter_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            $this->addFlash('success', 'Newsletter edited successfully.');

            return $this->redirectToRoute('admin_newsletters');
        }

        return $this->render('admin/newsletter/edit.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_newsletter_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        $form = $this->createForm(NewsletterDeleteType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($newsletter);
            $em->flush();

            $this->addFlash('success', 'Newsletter deleted successfully.');

            return $this->redirectToRoute('admin_newsletters');
        }

        return $this->render('admin/newsletter/delete.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @Route("/{id}/add_image", name="admin_newsletter_add_image", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param integer $id
     * @return RedirectResponse|Response
     */
    public function addImageAction(Request $request, $id)
    {
        $newsletter = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->find($id);
        $this->checkNewsletter($newsletter);

        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $oldImage = $newsletter->getImage();
            if ($oldImage) {
                $newsletter->setImage(null);
                $em->persist($newsletter);
                $em->remove($oldImage);
                $em->flush();
            }

            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/newsletter/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);
            $newsletter->setImage($image);

            $em->persist($newsletter);
            $em->persist($image);
            $em->flush();

            $this->addFlash('success_image', 'Image added successfully.');

            return $this->redirectToRoute('admin_newsletter', [
                'id' => $id
            ]);
        }

        return $this->render('admin/newsletter/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }

    /**
     * @param $newsletter
     */
    private function checkNewsletter($newsletter)
    {
        if (!$newsletter) {
            throw $this->createNotFoundException('Newsletter Not Found.');
        }
    }
}
