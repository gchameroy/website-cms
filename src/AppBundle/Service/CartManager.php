<?php

namespace AppBundle\Service;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Product;
use AppBundle\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CartManager
 * @package AppBundle\Service
 */
class CartManager
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Session $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * @param $product
     * @return Cart
     */
    public function addProduct($product)
    {
        $product = $this->em
            ->getRepository(Product::class)
            ->find($product);
        $this->checkProduct($product);

        $cart = $this->getCurrentCart();
        $cart->addProduct($product);

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    /**
     * @return Cart
     */
    public function getCurrentCart()
    {
        $token = $this->session->get('token');
        if (isset($token)) {
            $cart = $this->em
                ->getRepository(Cart::class)
                ->findOneByToken($token);
            if (!$cart) {
                $cart = $this->createCart();
                $this->session->set('token', $cart->getToken());
            }
        } else {
            $cart = $this->createCart();
            $this->session->set('token', $cart->getToken());
        }

        return $cart;
    }

    /**
     * @return Cart
     */
    public function createCart()
    {
        $token = $this->generateToken();

        $cart = new Cart();
        $cart->setToken($token);

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    /**
     * @param $token
     * @return Cart
     */
    public function findOneByToken($token)
    {
        $cart = $this->em->getRepository(CartRepository::class)
            ->findOneByToken($token);

        if (!$cart) {
            return $this->createCart();
        }

        return $cart;
    }

    /**
     * @return string
     */
    private function generateToken()
    {
        return password_hash(uniqid(random_bytes(5)), PASSWORD_BCRYPT);
    }

    /**
     *@param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw new NotFoundHttpException('Product Not Found.');
        }
    }
}
