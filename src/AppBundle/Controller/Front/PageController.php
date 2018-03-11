<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Manager\PartnerManager;
use AppBundle\Entity\Contact;
use AppBundle\Form\Type\Contact\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Swift_Mailer;

class PageController extends Controller
{
    /**
     * @Route("/presentation", name="front_presentation")
     * @Method({"GET"})
     * @return Response
     */
    public function presentationAction()
    {
        return $this->render('front/page/presentation.html.twig');
    }

    /**
     * @Route("/magasin", name="front_shop")
     * @Method({"GET"})
     * @return Response
     */
    public function shopAtion(Request $request, Swift_Mailer $mailer)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $message = (new \Swift_Message('Demande de contact'))
                ->setFrom('form-contact@coutellerie-legendre.fr')
                ->setTo('form-contact@coutellerie-legendre.fr')
                ->setBody(
                    $this->renderView('front/emails/contact.html.twig', [
                        'contact' => $contact
                    ]), 'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('front_home');
        }

        return $this->render('front/page/shop.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/partenaires", name="front_partners")
     * @Method({"GET"})
     * @param $partnerManager PartnerManager
     * @return Response
     */
    public function partnersAction(PartnerManager $partnerManager)
    {
        $partners = $partnerManager->getList();

        return $this->render('front/page/partners.html.twig', [
            'partners' => $partners
        ]);
    }

    /**
     * @Route("/cgv-cgu", name="front_privacy")
     * @Method({"GET"})
     * @return Response
     */
    public function privacyAction()
    {
        return $this->render('front/page/privacy.html.twig');
    }
}
