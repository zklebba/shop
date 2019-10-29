<?php

namespace App\Controller\Api;

use App\Service\CheckoutProcessService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class CheckoutController extends AbstractFOSRestController
{

    /**
     * @var CheckoutProcessService
     */
    private $checkoutService;

    public function __construct(CheckoutProcessService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * @Rest\Post("/checkout")
     *
     * @Rest\RequestParam(name="order")
     *
     * @Rest\View()
     *
     * @param array $order
     * @throws
     * @return array
     */
    public function checkoutAction($order)
    {
        $order = $this->checkoutService->process($order);

        return [
            'payment' => $order->getPayment()->getData(),
            'orderId' => $order->getId(),
        ];
    }
}
