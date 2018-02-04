<?php

namespace AppBundle\Controller\Professional;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/sign-in", name="professional_login")
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('professional_user_files');
        }

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('professional/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/sign-out", name="professional_logout")
     * @Method("GET")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/login-check", name="professional_login_check")
     * @Method("POST")
     */
    public function loginCheckAction()
    {

    }
}
