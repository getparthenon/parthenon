<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
