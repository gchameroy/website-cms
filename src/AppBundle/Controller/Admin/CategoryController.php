<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Form\Type\Category\CategoryDeleteType;
use AppBundle\Form\Type\Category\CategoryPublishType;
use AppBundle\Form\Type\Category\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function listAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/add", name="admin_categories_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, EntityManagerInterface $entityManager)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lastCategory = $entityManager
                ->getRepository(Category::class)
                ->findLast();
            $position = $lastCategory ? $lastCategory->getPosition() + 1 : 1;

            $category->setPosition($position);
            $entityManager->persist($category);
            $entityManager->flush();

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
    public function publishAction(Request $request, $id)
    {
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
    public function editAction(Request $request, $id)
    {
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
     * @Route("/move", name="admin_category_move")
     * @Method({"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function upAction(Request $request, EntityManagerInterface $entityManager)
    {
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('category-move', $token)) {
            return $this->redirectToRoute('admin_categories');
        }

        $category = $entityManager
            ->getRepository(Category::class)
            ->find($request->request->get('category'));
        $this->checkCategory($category);

        $direction = $request->request->get('direction');
        if (!in_array($direction, ['up', 'down'])) {
            return $this->redirectToRoute('admin_categories');
        }

        if ($direction == 'up') {
            if ($category->getPosition() == 1) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $entityManager->getRepository(Category::class)
                ->findOneByPosition($category->getPosition() - 1);
        } else {
            $last = $entityManager->getRepository(Category::class)
                ->findLast();
            if ($category === $last) {
                return $this->redirectToRoute('admin_categories');
            }

            /** @var Category $category2 */
            $category2 = $entityManager->getRepository(Category::class)
                ->findOneByPosition($category->getPosition() + 1);
        }

        $position = $category->getPosition();
        $position2 = $category2->getPosition();

        $category->setPosition(null);
        $category2->setPosition(null);
        $entityManager->persist($category);
        $entityManager->persist($category2);
        $entityManager->flush();

        $category->setPosition($position2);
        $category2->setPosition($position);
        $entityManager->persist($category);
        $entityManager->persist($category2);
        $entityManager->flush();

        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/{id}/delete", name="admin_category_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request)
    {
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
    private function checkCategory($category)
    {
        if (!$category) {
            throw $this->createNotFoundException('Category Not Found.');
        }
    }
}
