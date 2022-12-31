<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

use Parthenon\Common\Exception\GeneralException;
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
        private Config $config,
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

        try {
            $transaction = $this->transactionCloud->getTransactionById($request->get($this->config->getPaymentIdParameter()));
        } catch (\Throwable $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }

        if (!in_array($transaction->getTransactionStatus(), ['SUBSCRIPTION_STATUS_ACTIVE', 'ONE_TIME_PAYMENT_STATUS_PAID'])) {
            throw new GeneralException('status is not paid');
        }

        $subscription->setActive(true);
        $subscription->setStatus(Subscription::STATUS_ACTIVE);
        $subscription->setPaymentId($request->get($this->config->getPaymentIdParameter()));
        $subscription->setCustomerId($request->get($this->config->getCustomerIdParameter()));
        $subscription->increaseValidUntil();
    }
}
