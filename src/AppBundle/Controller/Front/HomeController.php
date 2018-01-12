<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Category;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\Contact\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends Controller
{
    /**
     * @Route("/", name="front_home")
     * @Method({"GET","POST"})
     * @return Response
     */
    public function homeAction() {
        return $this->render('front/home/home.html.twig');
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

    /**
     * @Route("/sitemap.{_format}", name="front_site_map", requirements={"_format": "xml"})
     * @Method({"GET"})
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function siteMapAction(EntityManagerInterface $em) {
        /** @var Product $lastProduct */
        $lastProduct = $em->getRepository(Product::class)
            ->findLastPublished()[0];

        $urls = [];

        $urls[] = [
            'loc' => $this->generateUrl('front_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => $lastProduct->getPublishedAt()->format('d-m-Y'),
            'changefreq' => 'daily',
            'priority' => '0.5'
        ];

        $newsletters = $em->getRepository(Newsletter::class)
            ->findPublished();
        /** @var Newsletter $newsletter */
        foreach ($newsletters as $newsletter) {
            $urls[] = [
                'loc' => $this->generateUrl('front_newsletter', ['newsletter' => $newsletter->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $newsletter->getPublishedAt()->format('d-m-Y'),
                'changefreq' => 'daily',
                'priority' => '0.5'
            ];
        }

        $categories = $em->getRepository(Category::class)
            ->findPublished();
        /** @var Category $category */
        foreach ($categories as $category) {
            $nbPage = $this->getDoctrine()
                ->getRepository(Product::class)
                ->countNbPagePublishedByCategory($category);
            for ($page = 1; $page < $nbPage; $page++) {
                $urls[] = [
                    'loc' => $this->generateUrl('front_products', ['category' => $category->getId(), 'page' => $page], UrlGeneratorInterface::ABSOLUTE_URL),
                    'lastmod' => $category->getPublishedAt()->format('d-m-Y'),
                    'changefreq' => 'daily',
                    'priority' => '0.5'
                ];
            }
        }


        $products = $em->getRepository(Product::class)
            ->findPublished();
        /** @var Product $product */
        foreach ($products as $product) {
            $urls[] = [
                'loc' => $this->generateUrl('front_product', ['category' => $product->getCategory()->getId(), 'product' => $product->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $product->getPublishedAt()->format('d-m-Y'),
                'changefreq' => 'daily',
                'priority' => '0.5'
            ];
        }

        $urls[] = [
            'loc' => $this->generateUrl('front_home', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => '2017-11-13',
            'changefreq' => 'daily',
            'priority' => '0.5'
        ];

        $urls[] = [
            'loc' => $this->generateUrl('front_privacy', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'lastmod' => '2017-11-13',
            'changefreq' => 'daily',
            'priority' => '0.5'
        ];

        return $this->render('front/layout/site-map.xml.twig', [
            'urls' => $urls
        ]);
    }
}
