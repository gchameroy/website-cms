<?php
namespace AppBundle\Controller\Client;

use AppBundle\Entity\CartProduct;
use AppBundle\Entity\DeliveryZone;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderProduct;
use AppBundle\Entity\User;
use AppBundle\Entity\UserOffer;
use AppBundle\Form\Type\AddressType;
use AppBundle\Form\Type\DeliveryZone\DeliveryZoneType;
use AppBundle\Form\Type\UserType;
use AppBundle\Repository\OrderRepository;
use AppBundle\Service\CartManager;
use AppBundle\Service\EmailProvider;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

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
            $em = $this->getDoctrine()->getManager();
            $defaultOffer = $em->getRepository(UserOffer::class)
                ->findOneBy(['label' => 'Sans offre']);

            $user = $form->getData();
            $date = explode('/', $user->getBirthDate());
            $date = $date[2].'-'.$date[1].'-'.$date[0];
            $user->setBirthDate(new \DateTime($date));
            $user->setOffer($defaultOffer);

            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            $token = new UsernamePasswordToken($user, null, 'user', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

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
        /** @var User $user */
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
            ->add('deliveryZone', EntityType::class, [
                'class' => DeliveryZone::class,
                'choice_label' => function (DeliveryZone $deliveryZone) {
                    return sprintf(
                        '%s (%s€)',
                        $deliveryZone->getName(),
                        number_format($deliveryZone->getPrice(), 2, ',', ' ')
                    );
                }
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

            $user->setDeliveryAddress($data['deliveryAddress'])
                ->setDeliveryZone($data['deliveryZone']);

            if ($data['isBillingAddress']) {
                $user->setBillingAddress($data['deliveryAddress']);
            } else{
                $em->persist($data['billingAddress']);
                $user->setBillingAddress($data['billingAddress']);
            }

            $cart = $cartManager->getCurrentCart();
            $cart->setComment($data['comment']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('client_order_recap');
        }

        return $this->render('client/address.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/order-recap", name="client_order_recap")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManager $em
     * @param CartManager $cartManager
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function orderRecap(Request $request, EntityManager $em, CartManager $cartManager, EmailProvider $emailProvider): Response
    {
        $cart = $cartManager->getCurrentCart();
        $token = $request->request->get('token');
        if (!$this->isCsrfTokenValid('client-order-recap', $token)) {
            return $this->render('client/order/recap.html.twig', [
                'cart' => $cart
            ]);
        }

        /** @var User $user */
        $user = $this->getUser();

        $order = (new Order())
            ->setUser($user)
            ->setDeliveryAddress($user->getDeliveryAddress())
            ->setBillingAddress($user->getBillingAddress())
            ->setDeliveryZone($user->getDeliveryZone())
            ->setComment($cart->getComment());
        $em->persist($order);

        /** @var CartProduct $cartProduct */
        foreach ($cart->getCartProducts() As $cartProduct) {
            $orderProduct = (new OrderProduct())
                ->setQuantity($cartProduct->getQuantity())
                ->setProduct($cartProduct->getProduct())
                ->setOrder($order);
            $em->persist($orderProduct);
        }

        $cart->setOrderedAt(new \DateTime())
            ->setToken(null);
        $em->persist($cart);
        $em->flush();

        $emailProvider->sendAdminNewOrder();
        $emailProvider->sendClientOrderConfirmation($user);

        return $this->redirectToRoute('client_order_confirmation', [
            'orderId' => $order->getId()
        ]);
    }

    /**
     * @Route("/order-confirmation/{orderId}", name="client_order_confirmation", requirements={"orderId": "\d+"})
     * @Method({"GET"})
     * @param int $orderId
     * @return Response
     */
    public function orderConfirmation(int $orderId)
    {
        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        /** @var Order|null $order */
        $order = $orderRepository->find($orderId);
        $this->checkOrder($order);

        return $this->render('client/order/confirmation.html.twig', [
            'order' => $order
        ]);
    }

    private function checkOrder(?Order $order)
    {
        if (!$order) {
            throw new NotFoundHttpException('Order Not Found.');
        }
    }
}
