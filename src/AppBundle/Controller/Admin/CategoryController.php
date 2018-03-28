<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\Category\CategoryDeleteType;
use AppBundle\Form\Type\Category\CategoryPublishType;
use AppBundle\Form\Type\Category\CategoryType;
use AppBundle\Manager\CategoryManager;
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
     * @param CategoryManager $categoryManager
     * @return Response
     */
    public function listAction(CategoryManager $categoryManager)
    {
        $categories = $categoryManager->getList();

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="admin_categories_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CategoryManager $categoryManager
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, CategoryManager $categoryManager)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $position = $categoryManager->getNextPosition();
            $category->setPosition($position);

            $categoryManager->save($category);

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
     * @param CategoryManager $categoryManager
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, CategoryManager $categoryManager, int $id)
    {
        $category = $categoryManager->get($id);

        $form = $this->createForm(CategoryPublishType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPublishedAt(new \DateTime());
            $categoryManager->save($category);

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/publish.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/unpublish", name="admin_category_unpublish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CategoryManager $categoryManager
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, CategoryManager $categoryManager, int $id)
    {
        $category = $categoryManager->get($id);

        $form = $this->createForm(CategoryPublishType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPublishedAt(null);
            $categoryManager->save($category);

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/unpublish.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CategoryManager $categoryManager
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, CategoryManager $categoryManager, int $id)
    {
        $category = $categoryManager->get($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryManager->save($category);

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/move", name="admin_category_move")
     * @Method({"POST"})
     * @param Request $request
     * @param CategoryManager $categoryManager
     * @return RedirectResponse|Response
     */
    public function upAction(Request $request, CategoryManager $categoryManager)
    {
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('category-move', $token)) {
            return $this->redirectToRoute('admin_categories');
        }

        $category = $categoryManager->get($request->request->get('category'));

        $direction = $request->request->get('direction');
        if (!in_array($direction, ['up', 'down'])) {
            return $this->redirectToRoute('admin_categories');
        }

        if ($direction == 'up') {
            if ($category->getPosition() == 1) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $categoryManager->getOneByPosition($category->getPosition() - 1);
        } else {
            $last = $categoryManager->getLast();
            if ($category === $last) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $categoryManager->getOneByPosition($category->getPosition() + 1);
        }

        $position = $category->getPosition();
        $position2 = $category2->getPosition();

        $category->setPosition(null);
        $category2->setPosition(null);
        $categoryManager->save($category);
        $categoryManager->save($category2);

        $category->setPosition($position2);
        $category2->setPosition($position);
        $categoryManager->save($category);
        $categoryManager->save($category2);

        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/{id}/delete", name="admin_category_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param CategoryManager $categoryManager
     * @return RedirectResponse|Response
     */
    public function deleteAction(int $id, Request $request, CategoryManager $categoryManager)
    {
        $category = $categoryManager->get($id);

        $form = $this->createForm(CategoryDeleteType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryManager->remove($category);

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/delete.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }
}
