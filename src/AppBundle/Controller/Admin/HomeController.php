<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/admin", name="admin_home")
     * @Method({"GET"})
     * @return Response
     */
    public function homeAction() {
        return $this->render('admin/home.html.twig');
    }
}
