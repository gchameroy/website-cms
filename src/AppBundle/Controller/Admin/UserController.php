<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\User\UserEditType;
use AppBundle\Form\Type\User\UserType;
use AppBundle\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="admin_users")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction(): Response
    {
        $users = $this->userManager->getList();

        return $this->render('admin/user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/add", name="admin_users_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request): Response
    {
        $user = $this->userManager->getNew();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password)
                ->eraseCredentials();
            $this->userManager->save($user);

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('admin/user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param int $id
     * @return Response
     */
    public function viewAction(int $id): Response
    {
        $user = $this->userManager->get($id);

        return $this->render('admin/user/view.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(int $id, Request $request): Response
    {
        $user = $this->userManager->get($id);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->save($user);

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
