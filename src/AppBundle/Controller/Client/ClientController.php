<?php
namespace AppBundle\Controller\Client;

use AppBundle\Entity\Order;
use AppBundle\Entity\OrderProduct;
use AppBundle\Entity\User;
use AppBundle\Form\Type\AddressType;
use AppBundle\Form\Type\UserType;
use AppBundle\Service\CartManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    /**
     * @Route("/register", name="client_register")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function registerAction(Request $request)
    {
        $email = $request->query->get('email');
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setBirthDate(new \DateTime($user->getBirthDate()));

            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé avec succès.');
            return $this->redirectToRoute('client_login');
        }

        return $this->render('client/register.html.twig', [
            'form' => $form->createView(),
            'email' => $email
        ]);
    }

    /**
     * @Route("/register_email", name="client_register_email")
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function registerEmailAction(Request $request)
    {
        $email = $request->request->get('email');
        $reg = '/^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,10}$/';

        if ($email === null || preg_match($reg, $email) !== 1) {
            return new JsonResponse([false]);
        }
        $url = $this->generateUrl('client_register', [
            'email' => $email
        ]);
        return new JsonResponse([true, $url]);
    }

    /**
     * @Route("/register_address", name="client_register_address")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CartManager $cartManager
     * @return Response|RedirectResponse
     */
    public function registerAddressAction(Request $request, CartManager $cartManager)
    {
        $cart = $cartManager->getCurrentCart();
        $user = $this->getUser();

        $isBillingAddress = false;
        if ($user->getDeliveryAddress() && $user->getBillingAddress()) {
            if ($user->getDeliveryAddress()->getId() == $user->getBillingAddress()->getId()) {
                $isBillingAddress = true;
            }
        }
        else {
            if (!$user->getDeliveryAddress() && !$user->getBillingAddress()) {
                $isBillingAddress = true;
            }
        }

        $form = $this->createFormBuilder()
            ->add('deliveryAddress', AddressType::class, [
                'data' => $user->getDeliveryAddress()
            ])
            ->add('billingAddress', AddressType::class, [
                'data' => $user->getBillingAddress()
            ])
            ->add('isBillingAddress', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'checked' => $isBillingAddress
                ]
            ])
            ->add('comment', TextareaType::class, [
                'required' => false
            ])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($data['deliveryAddress']);
            $em->flush();
            $user->setDeliveryAddress($data['deliveryAddress']);

            $order = (new Order())
                ->setUser($user)
                ->setDeliveryAddress($data['deliveryAddress'])
                ->setComment($data['comment']);

            foreach ($cart->getProducts() As $cartProduct) {
                $orderProduct = (new OrderProduct())
                    ->setQuantity($cartProduct->getQuantity())
                    ->setPrice($cartProduct->getPrice())
                    ->setProduct($cartProduct->getProduct());
                $em->persist($orderProduct);
                $em->flush();
                $order->addOrderProduct($orderProduct);
            }
            $em->persist($order);
            $em->flush();

            if ($data['isBillingAddress']) {
                $user->setBillingAddress($data['deliveryAddress']);
                $order->setBillingAddress($data['deliveryAddress']);
            }
            else{
                $em->persist($data['billingAddress']);
                $em->flush();
                $user->setBillingAddress($data['billingAddress']);
                $order->setBillingAddress($data['billingAddress']);
            }
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('client_register_address');
        }

        return $this->render('client/address.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}