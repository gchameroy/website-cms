<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\CategoryAttribute;
use AppBundle\Form\Type\CategoryAttribute\CategoryAttributeDeleteType;
use AppBundle\Form\Type\CategoryAttribute\CategoryAttributeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/categories_attribute")
 */
class CategoryAttributeController extends Controller
{
    /**
     * @Route("/", name="admin_categories_attribute")
     * @return Response
     */
    public function listAction() {
        $categoriesAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->findAll();

        return $this->render('admin/category_attribute/list.html.twig', [
            'categoriesAttribute' => $categoriesAttribute
        ]);
    }

    /**
     * @Route("/add", name="admin_categories_attribute_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $categoryAttribute = new CategoryAttribute();

        $form = $this->createForm(CategoryAttributeType::class, $categoryAttribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoryAttribute);
            $em->flush();

            $this->addFlash('success', 'Category Attribute added successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/category_attribute/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_attribute_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $categoryAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->find($id);
        $this->checkCategoryAttribute($categoryAttribute);

        $form = $this->createForm(CategoryAttributeType::class, $categoryAttribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoryAttribute);
            $em->flush();

            $this->addFlash('success', 'Category Attribute edited successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/category_attribute/edit.html.twig', [
            'form' => $form->createView(),
            'categoryAttribute' => $categoryAttribute,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_category_attribute_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $categoryAttribute = $this->getDoctrine()
            ->getRepository(CategoryAttribute::class)
            ->find($id);
        $this->checkCategoryAttribute($categoryAttribute);

        $form = $this->createFormBuilder($categoryAttribute)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryAttribute);
            $em->flush();

            $this->addFlash('success', 'Category Attribute deleted successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/category_attribute/delete.html.twig', [
            'form' => $form->createView(),
            'categoryAttribute' => $categoryAttribute,
        ]);
    }

    /**
     * @param $categoryAttribute
     */
    private function checkCategoryAttribute($categoryAttribute) {
        if (!$categoryAttribute) {
            throw $this->createNotFoundException('Category Attribute Not Found.');
        }
    }
}
