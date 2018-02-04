<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Entity\UserFile;
use AppBundle\Form\Type\User\UserEditType;
use AppBundle\Form\Type\User\UserType;
use AppBundle\Form\Type\UserFile\UserFileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="admin_users")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction() {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/user/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/add", name="admin_users_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password)
                ->eraseCredentials();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{user}/add_document", name="admin_user_add_file", requirements={"user": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param int $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse|Response
     */
    public function addFileAction(int $user, Request $request, EntityManagerInterface $entityManager) {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($user);
        $this->checkUser($user);

        $userFile = new UserFile($user);
        $form = $this->createForm(UserFileType::class, $userFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $userFile->getFile()->getPath();
            $fileName = md5(uniqid(null, true));
            $extension = $file->guessExtension();
            $filePath = sprintf(
                '%s/../uploads/user-files/%s',
                $this->get('kernel')->getRootDir(),
                $userFile->getUser()->getId()
            );

            $file->move($filePath, $fileName);
            $userFile->getFile()->setPath($fileName);
            $userFile->getFile()->setExtension($extension);

            $entityManager->persist($userFile);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/user-file/add.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id) {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        $this->checkUser($user);

        return $this->render('admin/user/view.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        $this->checkUser($user);

        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @param $user
     */
    private function checkUser($user) {
        if (!$user) {
            throw $this->createNotFoundException('User Not Found.');
        }
    }
}
