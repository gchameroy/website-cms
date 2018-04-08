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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/categories")
 */
class CategoryController Extends Controller
{
    /** @var CategoryManager */
    private $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * @Route("/", name="admin_categories")
     * @return Response
     */
    public function listAction(): Response
    {
        $categories = $this->categoryManager->getList();

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
    public function addAction(Request $request): Response
    {
        $category = $this->categoryManager->getNew();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $position = $this->categoryManager->getNextPosition();
            $category->setPosition($position);

            $this->categoryManager->save($category);

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add-modal", name="admin_categories_add_modal", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function addModalAction(Request $request): Response
    {
        $category = $this->categoryManager->getNew();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryManager->save($category);

            return new JsonResponse([
                'success' => true,
                'id' => $category->getId(),
                'label' => $category->getLabel()
            ]);
        }

        return $this->render("admin/product/modal/add_category.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_category_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, int $id): Response
    {
        $category = $this->categoryManager->get($id);

        $form = $this->createForm(CategoryPublishType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPublishedAt(new \DateTime());
            $this->categoryManager->save($category);

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
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, int $id): Response
    {
        $category = $this->categoryManager->get($id);

        $form = $this->createForm(CategoryPublishType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setPublishedAt(null);
            $this->categoryManager->save($category);

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
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $category = $this->categoryManager->get($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryManager->save($category);

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
     * @return RedirectResponse|Response
     */
    public function upAction(Request $request): Response
    {
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('category-move', $token)) {
            return $this->redirectToRoute('admin_categories');
        }

        $category = $this->categoryManager->get($request->request->get('category'));

        $direction = $request->request->get('direction');
        if (!in_array($direction, ['up', 'down'])) {
            return $this->redirectToRoute('admin_categories');
        }

        if ($direction == 'up') {
            if ($category->getPosition() == 1) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $this->categoryManager->getOneByPosition($category->getPosition() - 1);
        } else {
            $last = $this->categoryManager->getLast();
            if ($category === $last) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $this->categoryManager->getOneByPosition($category->getPosition() + 1);
        }

        $position = $category->getPosition();
        $position2 = $category2->getPosition();

        $category->setPosition(null);
        $category2->setPosition(null);
        $this->categoryManager->save($category);
        $this->categoryManager->save($category2);

        $category->setPosition($position2);
        $category2->setPosition($position);
        $this->categoryManager->save($category);
        $this->categoryManager->save($category2);

        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/delete", name="admin_category_delete")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction(Request $request): Response
    {
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('category-move', $token)) {
            return $this->redirectToRoute('admin_categories');
        }

        $category = $this->categoryManager->get($request->request->get('category'), false);
        $this->categoryManager->remove($category);

        return $this->redirectToRoute('admin_categories');
    }
}
