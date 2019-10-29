<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\OrdersService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class OrderController extends AbstractFOSRestController
{

    /**
     * @var OrdersService
     */
    private $ordersService;

    public function __construct(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }

    /**
     * @Rest\POST("/orders/access-ask")
     *
     * Sending access link to customer witch code to allow see customer orders.
     *
     * @Rest\View()
     *
     * @Rest\RequestParam(name="email")
     *
     * @param string $email
     *
     * @return array
     */
    public function askForAccess($email = '')
    {
        $this->ordersService->sendOrdersAccessCode($email);
        return ['status' => true];
    }

    /**
     * @Rest\Get("/orders/{accessCode}")
     *
     * @Rest\View()
     *
     * @param string $accessCode
     *
     * @return Order[]
     */
    public function getListAction($accessCode)
    {
        $orders = $this->ordersService->getOrders($accessCode);

        if ($orders === null) {
            throw $this->createAccessDeniedException();
        } else {
            return $orders;
        }
    }
}
