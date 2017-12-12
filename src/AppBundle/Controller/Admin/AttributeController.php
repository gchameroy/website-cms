<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\CategoryAttribute;
use AppBundle\Form\Type\Attribute\AttributeType;
use AppBundle\Form\Type\CategoryAttribute\CategoryAttributeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/attributes")
 */
class AttributeController extends Controller
{
    /**
     * @Route("/add", name="admin_attributes_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $attribute = new Attribute();

        if (null !== $request->query->get('id')) {
            $category_id = $request->query->get('id');
            $categoryAttribute = $this->getDoctrine()->getRepository(CategoryAttribute::class)->find($category_id);
            $attribute->setCategoryAttribute($categoryAttribute);
        }

        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'Attribute added successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/attribute/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_attribute_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $attribute = $this->getDoctrine()
            ->getRepository(Attribute::class)
            ->find($id);
        $this->checkAttribute($attribute);

        $form = $this->createForm(AttributeType::class, $attribute);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($attribute);
            $em->flush();

            $this->addFlash('success', 'Attribute edited successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/attribute/edit.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_attribute_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $attribute = $this->getDoctrine()
            ->getRepository(Attribute::class)
            ->find($id);
        $this->checkAttribute($attribute);

        $form = $this->createFormBuilder($attribute)->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($attribute);
            $em->flush();

            $this->addFlash('success', 'Attribute deleted successfully.');

            return $this->redirectToRoute('admin_categories_attribute');
        }

        return $this->render('admin/attribute/delete.html.twig', [
            'form' => $form->createView(),
            'attribute' => $attribute,
        ]);
    }

    /**
     * @Route("/add_category", name="admin_attribute_add_category", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addCategoryAttributeAction(Request $request) {
        $categoryAttribute = new CategoryAttribute();

        $form = $this->createForm(CategoryAttributeType::class, $categoryAttribute);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $categoryAttribute = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($categoryAttribute);
                $em->flush();

                return new JsonResponse([
                    'success' => true,
                    'id' => $categoryAttribute->getId(),
                    'label'  => $categoryAttribute->getLabel()
                ]);
            }
        }

        return $this->render("admin/attribute/modal/add_category_attribute.html.twig", array(
            'form'  =>  $form->createView()
        ));
    }

    /**
     * @param $attribute
     */
    private function checkAttribute($attribute) {
        if (!$attribute) {
            throw $this->createNotFoundException('Attribute Not Found.');
        }
    }
}
