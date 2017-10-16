<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Contact;
use AppBundle\Form\Type\Contact\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="front_home")
     * @Method({"GET","POST"})
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function homeAction(Request $request, \Swift_Mailer $mailer) {
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

            $this->addFlash('success', 'Email send successfully.');

            return $this->redirectToRoute('front_home');
        }

        return $this->render('front/home/home.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param int $max
     * @return Response
     */
    public function productsAction($max = 3) {
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findLastPublished($max);

        return $this->render('front/product/partial/last-products.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @param int $max
     * @return Response
     */
    public function newslettersAction($max = 2) {
        $newsletters = $this->getDoctrine()
            ->getRepository('AppBundle:Newsletter')
            ->findAll($max, 1, 'desc');

        return $this->render('front/newsletter/partial/last-newsletters.html.twig', [
            'newsletters' => $newsletters
        ]);
    }
}
