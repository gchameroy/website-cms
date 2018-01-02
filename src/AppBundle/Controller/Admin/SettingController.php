<?php 
namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/setting")
 */
class SettingController extends Controller
{

    /**
     * @Route("/", name="setting")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction() {
        $users = $this->getDoctrine()
            ->getRepository(Setting::class)
            ->findAll();

        return $this->render('admin/setting/list.html.twig', [
            'setting' => $setting
        ]);
    }


    
    /**
     * @Route("/add", name="setting_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request) {
        $setting = new Setting();

        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password)
                ->eraseCredentials();

            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            $this->addFlash('success', 'setting added successfully.');

            return $this->redirectToRoute('setting', [
                'id' => $setting->getId()
            ]);
        }

        return $this->render('admin/setting/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }







}
