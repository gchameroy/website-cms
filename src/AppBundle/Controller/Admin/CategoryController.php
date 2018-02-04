<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\Category\CategoryDeleteType;
use AppBundle\Form\Type\Category\CategoryPublishType;
use AppBundle\Form\Type\Category\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/categories")
 */
class CategoryController Extends Controller
{
    /**
     * @Route("/", name="admin_categories")
     * @return Response
     */
    public function listAction() {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll(0, null, 'asc', false);

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="admin_categories_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories', [
                'id' => $category->getId(),
            ]);
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_category_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, $id) {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        $this->checkCategory($category);

        $form = $this->createForm(CategoryPublishType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPublishedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/publish.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        $this->checkCategory($category);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_category_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        $this->checkCategory($category);

        $form = $this->createForm(CategoryDeleteType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/delete.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @param $category
     */
    private function checkCategory($category) {
        if (!$category) {
            throw $this->createNotFoundException('Category Not Found.');
        }
    }
}
