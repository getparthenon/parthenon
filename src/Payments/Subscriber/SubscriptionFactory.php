<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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

namespace Parthenon\Payments\Subscriber;

use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\PriceProviderInterface;

final class SubscriptionFactory implements SubscriptionFactoryInterface
{
    public function __construct(private PriceProviderInterface $priceProvider)
    {
    }

    public function createFromPlanAndPaymentSchedule(Plan $plan, string $paymentSchedule): Subscription
    {
        $priceId = $this->priceProvider->getPriceId($plan, $paymentSchedule);

        $subscription = new Subscription();
        $subscription->setPlanName($plan->getName());
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setPriceId($priceId);

        return $subscription;
    }
}
