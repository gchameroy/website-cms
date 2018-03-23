<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use AppBundle\Form\Type\ImageType;
use AppBundle\Form\Type\Newsletter\NewsletterDeleteType;
use AppBundle\Form\Type\Newsletter\NewsletterPublishType;
use AppBundle\Form\Type\Newsletter\NewsletterType;
use AppBundle\Manager\NewsletterManager;
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
    /** @var NewsletterManager */
    private $newsletterManager;

    public function __construct(NewsletterManager $newsletterManager)
    {
        $this->newsletterManager = $newsletterManager;
    }

    /**
     * @Route("/", name="admin_newsletters")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction(): Response
    {
        $newsletters = $this->newsletterManager->getList();

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
    public function addAction(Request $request): Response
    {
        $newsletter = $this->newsletterManager->getNew();

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter = $this->newsletterManager->save($newsletter);

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
    public function viewAction(int $id): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        return $this->render('admin/newsletter/view.html.twig', [
            'newsletter' => $newsletter
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_newsletter_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function publishAction(int $id, Request $request): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        $form = $this->createForm(NewsletterPublishType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setPublishedAt(new \DateTime());
            $this->newsletterManager->save($newsletter);

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
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function unpublishAction(int $id, Request $request): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        if (null === $newsletter->getPublishedAt()) {
            return $this->redirectToRoute('admin_newsletters');
        }

        $form = $this->createForm(NewsletterPublishType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setPublishedAt(null);
            $this->newsletterManager->save($newsletter);

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
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(int $id, Request $request): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsletterManager->save($newsletter);

            return $this->redirectToRoute('admin_newsletter', [
                'id' => $newsletter->getId(),
            ]);
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
    public function deleteAction(int $id, Request $request): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        $form = $this->createForm(NewsletterDeleteType::class, $newsletter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->newsletterManager->remove($newsletter);

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
     * @param integer $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addImageAction(int $id, Request $request): Response
    {
        $newsletter = $this->newsletterManager->get($id);

        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter = $this->newsletterManager->removeOldImage($newsletter);

            /** @var UploadedFile $file */
            $file = $image->getPath();
            $fileName = md5(uniqid(null, true));
            $filePath = $this->get('kernel')->getRootDir() . '/../uploads/newsletter/';
            $file->move($filePath, $fileName);
            $image->setPath($fileName);

            $newsletter->setImage($image);
            $newsletter = $this->newsletterManager->save($newsletter);

            return $this->redirectToRoute('admin_newsletter', [
                'id' => $newsletter->getId()
            ]);
        }

        return $this->render('admin/newsletter/modal/add_image.html.twig', [
            'form' => $form->createView(),
            'newsletter' => $newsletter,
        ]);
    }
}
