<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Contact;
use AppBundle\Form\Type\Contact\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="front_contact")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function viewAction(Request $request, \Swift_Mailer $mailer)
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

            return $this->redirectToRoute('front_contact');
        }

        return $this->render('front/contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
