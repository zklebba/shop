<?php

namespace App\Service\Payment\Gateway\Stripe;

use App\Dictionary\PaymentStatus;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Payment;
use App\Service\Payment\Gateway\GatewayInterface;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class StripeService implements GatewayInterface
{
    private $config;
    private $productsRepo;

    /**
     * @var RouterInterface
     */
    private $router;

    const PAYMENT_TYPE = 'stripe';

    public function __construct($secretApiKey, $productsRepo, $router, $config = [])
    {
        $this->config = $config;
        $this->productsRepo = $productsRepo;
        $this->router = $router;
        Stripe::setApiKey($secretApiKey);
    }

    protected function getLineItems($orderDetails)
    {
        $lines = [];

        foreach ($orderDetails as $detail) {
            /**
             * @var OrderDetails $detail
             */

            $lines[] = [
                'name' => $detail->getProduct()->getName(),
                'description' => $detail->getProduct()->getDescription(),
                'images' => [ $detail->getProduct()->getPicture() ],
                'amount' => intval($detail->getPrice() * 100),
                'currency' => $this->config['currency'],
                'quantity' => intval($detail->getQuantity()),
            ];
        }

        return $lines;
    }

    protected function getPaymentIntentData(Order $order)
    {
        $shipping = array_merge([
            'first_name' => '',
            'last_name' => '',
            'address' => '',
            'address_line_1' => '',
            'phone' => '',
            'country' => '',
            'city' => '',
            'post_code' => '',
        ], $order->getCustomer()->getShippingAddress());

        return [
            'metadata' => [
                'order_id' => $order->getId(),
                'order_number' => $order->getNumber(),
                'customer_id' => $order->getCustomer()->getId(),
                'order_payment_id' => $order->getPayment()->getId(),
                'order_comment' => $order->getComment(),
            ],

            'receipt_email' => $order->getCustomer()->getEmail(),
            'shipping' => [
                'address' => [
                    'line1' => $shipping['address'],
                    'city' => $shipping['city'],
                    'country' => $shipping['country'],
                    'line2' => $shipping['address_line_1'],
                    'postal_code' => $shipping['post_code'],
                ],
                'name' => $shipping['first_name'] . ' ' . $shipping['last_name'],
                'phone' => $shipping['phone']
            ],
        ];
    }

    public function createCheckoutSession(Order $order, $options = []): array
    {
        $successUrl = $this->router->generate(
            $this->config['route_success'],
            array_merge($this->config['route_success_vars'], [
                'orderId' => $order->getNumber(),
            ]),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $cancelUrl = $this->router->generate(
            $this->config['route_cancel'],
            array_merge($this->config['route_cancel_vars']),
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $sessionRequestData = array_merge([
            'line_items' => $this->getLineItems($order->getOrderDetails()),
            'payment_method_types' => $this->config['payment_method_types'],
            'customer_email' => $order->getCustomer()->getEmail(),
            'payment_intent_data' => $this->getPaymentIntentData($order),
            'locale' => $this->config['locale'],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], $options);

        $session = Session::create($sessionRequestData);

        return [
            'sessionId' => $session->id,
            'payment_intent' => $session->payment_intent,
        ];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return self::PAYMENT_TYPE;
    }

    public function getUpdatedPaymentStatus(Payment $payment): bool
    {
        $paymentIntentId = $payment->getData()['payment_intent'];

        if ($paymentIntentId) {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            $amount_received = $intent->amount_received;
            $status = $intent->status;
            $customer = $intent->customer;

            if ($status === 'succeeded') {
                $payment->setStatus(PaymentStatus::SUCCEEDED);
            } else if ($status === 'canceled') {
                $payment->setStatus(PaymentStatus::CANCELED);
            } else {
                $payment->setStatus(PaymentStatus::PROCESSING);
            }

            $payment->setBalance($payment->getBalance() + intval($amount_received / 100));

            $payment->setData(array_merge($payment->getData(), [
                'amount_received' => $amount_received,
                'status' => $status,
                'customer' => $customer,
            ]));

            return true;
        } else {
            return false;
        }
    }
}
