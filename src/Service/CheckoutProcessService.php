<?php

namespace App\Service;

use App\Dictionary\OrderStatus;
use App\Dictionary\PaymentStatus;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Payment;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use App\Service\Payment\Gateway\Stripe\StripeService;

class CheckoutProcessService
{
    /**
     * @var ProductRepository
     */
    private $productRepo;

    /**
     * @var OrderRepository
     */
    private $orderRepo;

    /**
     * @var StripeService
     */
    private $gateway;

    /**
     * @var CustomerRepository
     */
    private $customerRepo;

    /**
     * @var PaymentRepository
     */
    private $paymentRepo;

    public function __construct(
        ProductRepository $productRepo,
        OrderRepository $orderRepo,
        CustomerRepository $customerRepo,
        PaymentRepository $paymentRepo,
        StripeService $gateway
    )
    {
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
        $this->customerRepo = $customerRepo;
        $this->paymentRepo = $paymentRepo;
        $this->gateway = $gateway;
    }

    /**
     * Start order processing
     * - creating checkout data in database, start payment session
     *
     * @param $orderData
     * @param array $paymentOptions
     * @return Order
     * @throws \Exception
     */
    public function process($orderData, $paymentOptions = [])
    {
        $orderData = array_merge([
            'customer' => [],
            'details' => [],
            'comment' => '',
        ], $orderData);

        $this->orderRepo->startOrderTransaction();

        try {
            $details = $this->createOrderDetails($orderData['details']);
            $customer = $this->createCustomer($orderData['customer']);
            $toPaid = $this->getTotalToPay($orderData['details']);
            $payment = $this->openPayment($toPaid);

            $order = new Order();
            $order->setStatus(OrderStatus::OPEN);
            $order->setOrderDate(new \DateTime());
            $order->setNumber($this->orderRepo->genUUID());
            $order->setCustomer($customer);
            $order->setPayment($payment);
            $order->setComment($orderData['comment']);
            $order->addOrderDetails($details);

            $this->orderRepo->createOrder($order);

            $this->startPaymentSession($payment, $order, $paymentOptions);

            $this->orderRepo->commitOrderTransaction();

            return $order;
        } catch (\Exception $e) {
            $this->orderRepo->rollbackOrderTransaction();
            throw $e;
        }
    }

    /**
     * @param $customerData
     * @return Customer
     */
    protected function createCustomer($customerData)
    {
        $customerData = array_merge([
            'email' => '',
            'shipping_address' => [],
            'billing_address' => []
        ], $customerData);

        $customer = new Customer();

        $customer->setEmail($customerData['email']);
        $customer->setShippingAddress($customerData['shipping_address']);
        $customer->setBillingAddress($customerData['billing_address']);

        $this->customerRepo->save($customer);

        return $customer;
    }

    /**
     * @param $orderDetails
     * @return float|int
     */
    protected function getTotalToPay($orderDetails)
    {
        $orderDetails = array_merge([
            'quantity' => 0,
            'price' => 0,
        ], $orderDetails);

        $total = 0;

        foreach ($orderDetails as $detail) {
            $total += intval($detail['quantity']) * floatval($detail['price']);
        }

        return $total;
    }

    /**
     * @param float $toPay
     * @return Payment
     */
    protected function openPayment($toPay = 0.0)
    {
        $payment = new Payment();
        $payment->setType($this->gateway->getType());
        $payment->setStatus(PaymentStatus::OPEN);
        $payment->setBalance(-(floatval($toPay)));
        $payment->setData([]);

        $this->paymentRepo->save($payment);

        return $payment;
    }

    /**
     * @param $orderDetails
     * @return array
     */
    protected function createOrderDetails($orderDetails)
    {
        $details = [];
        $productIds = array_column($orderDetails, 'product');
        $products = $this->productRepo->getListByIds($productIds);

        foreach ($orderDetails as $key => $detail) {
            $product = $products[$key];
            $detailEntity = new OrderDetails();

            $detailEntity->setQuantity($orderDetails[$key]['quantity']);
            $detailEntity->setPrice($orderDetails[$key]['price']);
            $detailEntity->setProduct($product);

            $details[] = $detailEntity;
        }

        return $details;
    }

    /**
     * @param Payment $payment
     * @param Order $order
     * @param array $paymentOptions
     */
    protected function startPaymentSession(Payment $payment, Order $order, $paymentOptions = [])
    {
        $sessionData = $this->gateway->createCheckoutSession($order, $paymentOptions);
        $payment->setData(array_merge($payment->getData(), $sessionData));
        $this->paymentRepo->update($payment);
    }
}