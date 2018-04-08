<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ProductSkill;
use AppBundle\Form\Type\ProductSkill\ProductSkillType;
use AppBundle\Manager\ProductManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/products")
 */
class ProductSkillController extends Controller
{
    /** @var ProductManager */
    private $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * @Route("/{id}/add_skill", name="admin_product_skill_add", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addSkillAction(int $id, Request $request): Response
    {
        $product = $this->productManager->get($id);
        $skill = new ProductSkill();

        $form = $this->createForm(ProductSkillType::class, $skill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $skill->setProduct($product);
            $em = $this->getDoctrine()->getManager();
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/add_skill.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    /**
     * @Route("/{id}/edit_skill", name="admin_product_skill_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editSkillAction(int $id, Request $request): Response
    {
        $product = $this->productManager->get($id);

        $em = $this->getDoctrine()->getManager();
        /** @var ProductSkill $skill */
        $skill = $em->getRepository(ProductSkill::class)
            ->findOneBy([
                'id' => $request->query->get('skill'),
                'product' => $product
            ]);
        $this->checkSkill($skill);

        $form = $this->createForm(ProductSkillType::class, $skill);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($skill);
            $em->flush();

            return $this->redirectToRoute('admin_product', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('admin/product/modal/edit_skill.html.twig', [
            'form' => $form->createView(),
            'skill' => $skill
        ]);
    }

    /**
     * @Route("/{product}/delete_skill", name="admin_product_skill_delete", requirements={"product": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $product
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSkillAction(int $product, Request $request): Response
    {
        $product = $this->productManager->get($product);

        $em = $this->getDoctrine()->getManager();
        /** @var ProductSkill $skill */
        $skill = $em->getRepository(ProductSkill::class)
            ->findOneBy([
                'id' => $request->request->get('skill'),
                'product' => $product
            ]);
        $this->checkSkill($skill);

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('product-skill-delete', $token)) {
            $em->remove($skill);
            $em->flush();
        }

        return $this->redirectToRoute('admin_product', [
            'id' => $product->getId()
        ]);
    }

    private function checkSkill(?ProductSkill $skill): void
    {
        if (!$skill) {
            throw $this->createNotFoundException('Product Skill Not Found.');
        }
    }
}
