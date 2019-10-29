<?php

namespace App\Service\Payment\Gateway;

use App\Entity\Order;
use App\Entity\Payment;

interface GatewayInterface
{
    /**
     * @return array
     */
    public function createCheckoutSession(Order $order, $options = []): array;

    /**
     * @return boolean
     */
    public function getUpdatedPaymentStatus(Payment $payment): bool;

    /**
     * @return string
     */
    public function getType(): string;
}
