<?php

namespace App\Service;

use App\Dictionary\OrderStatus;
use App\Dictionary\PaymentStatus;
use App\Entity\Order;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Service\Email\OrdersAccessEmail;
use App\Service\Payment\Gateway\Stripe\StripeService;
use Symfony\Component\Routing\RouterInterface;

class OrdersService
{

    /**
     * @var OrderRepository
     */
    private $orderRepo;

    /**
     * @var StripeService
     */
    private $gateway;

    /**
     * @var PaymentRepository
     */
    private $paymentRepo;

    /**
     * @var CustomerRepository
     */
    private $customerRepo;

    /**
     * @var OrdersAccessEmail
     */
    private $accessEmail;

    /**
     * @var OrdersAccessKeyService
     */
    private $accessKeyGenerator;

    public function __construct(
        OrderRepository $orderRepo,
        CustomerRepository $customerRepository,
        PaymentRepository $paymentRepo,
        StripeService $gateway,
        OrdersAccessKeyService $accessKey,
        OrdersAccessEmail $accessEmail
    ) {
        $this->orderRepo = $orderRepo;
        $this->customerRepo = $customerRepository;
        $this->paymentRepo = $paymentRepo;
        $this->gateway = $gateway;
        $this->accessKeyGenerator = $accessKey;
        $this->accessEmail = $accessEmail;
    }

    public function getOrders($accessCode)
    {
        $email = $this->accessKeyGenerator->decodeAccessKey($accessCode);

        if ($email) {
            $orders = $this->orderRepo->getList($email);

            foreach ($orders as &$order) {
                /**
                 * @var Order $order
                 */

                if (in_array($order->getPayment()->getStatus(), [
                    PaymentStatus::OPEN,
                    PaymentStatus::PROCESSING
                ])) {
                    $order = $this->updatePaymentStatus($order);
                }
            }

            return $orders;
        } else {
            return null;
        }
    }

    public function sendOrdersAccessCode($email = ''): void
    {
        if ($this->customerRepo->customerExist($email)) {
            $this->accessEmail->send($email);
        }
    }

    protected function updatePaymentStatus(Order $order)
    {
        $payment = $this->paymentRepo->getById($order->getPayment()->getId());

        if ($this->gateway->getUpdatedPaymentStatus($payment)) {
            $paymentStatus = $payment->getStatus();

            switch ($paymentStatus) {
                case PaymentStatus::SUCCEEDED: {
                    $order->setStatus(OrderStatus::COMPLETED);
                } break;

                case PaymentStatus::PROCESSING: {
                    $order->setStatus(OrderStatus::IN_PROCESS);
                } break;

                case PaymentStatus::CANCELED: {
                    $order->setStatus(OrderStatus::CANCELLED);
                } break;
            }

            $this->paymentRepo->update($payment);
            $this->orderRepo->update($order);
        }

        return $order;
    }
}