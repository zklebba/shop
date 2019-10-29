<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    public function renderShopPage()
    {
        return $this->render('shop/index.html.twig');
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/basket", name="basket")
     */
    public function basket()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/category/{categoryId}", name="category")
     */
    public function category()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/product/{productId}", name="product")
     */
    public function product()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/checkout/{paymentReturn}/{orderId}", name="checkout", defaults={"paymentReturn"=null, "orderId"=null})
     */
    public function checkout()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/orders/{accessCode}", name="orders", defaults={"accessCode"=null})
     */
    public function orders()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->renderShopPage();
    }

    /**
     * @Route("/page/{pageId}", name="page")
     */
    public function page()
    {
        return $this->renderShopPage();
    }
}
