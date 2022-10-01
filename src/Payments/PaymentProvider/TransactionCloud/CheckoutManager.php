<?php

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

use Parthenon\Payments\Checkout;
use Parthenon\Payments\CheckoutInterface;
use Parthenon\Payments\CheckoutManagerInterface;
use Parthenon\Payments\Entity\Subscription;
use Symfony\Component\HttpFoundation\RequestStack;
use TransactionCloud\TransactionCloud;

final class CheckoutManager implements CheckoutManagerInterface
{
    public function __construct(
        private TransactionCloud $transactionCloud,
        private RequestStack $requestStack,
    ) {
    }

    public function createCheckoutForSubscription(Subscription $subscription, array $options = [], int $seats = 1): CheckoutInterface
    {
        $url = $this->transactionCloud->getPaymentUrlForProduct($subscription->getPriceId());

        return new Checkout($url);
    }

    public function handleSuccess(Subscription $subscription): void
    {
        $request = $this->requestStack->getCurrentRequest();

        $subscription->setPaymentId($request->get('paymentId'));
        $subscription->setCustomerId($request->get('customerId'));
    }
}
