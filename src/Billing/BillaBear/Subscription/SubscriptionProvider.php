<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Billing\BillaBear\Subscription;

use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Subscription\SubscriptionProviderInterface;

class SubscriptionProvider implements SubscriptionProviderInterface
{
    public function __construct(
        private SdkFactory $sdkFactory,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function getSubscriptionsForCustomer(CustomerInterface $customer): array
    {
        $response = $this->sdkFactory->createSubscriptionsApi()->getActiveForCustomer($customer->getExternalCustomerReference());
        $output = [];
        foreach ($response->getData() as $subscription) {
            $output[] = $this->buildSubscription($subscription);
        }

        return $output;
    }

    public function buildSubscription(\BillaBear\Model\Subscription $subscription): Subscription
    {
        $entity = $this->entityFactory->getSubscriptionEntity();
        $entity->setId($subscription->getId());
        $entity->setActive(true);
        $entity->setPaymentSchedule($subscription->getSchedule());
        $entity->setSeats($subscription->getSeatNumber());
        $entity->setPlanName($subscription->getPlan()?->getName());
        $entity->setValidUntil(new \DateTime($subscription->getValidUntil()));
        $entity->setCreatedAt(new \DateTime($subscription->getCreatedAt()));
        $entity->setUpdatedAt(new \DateTime($subscription->getUpdatedAt()));
        $entity->setCurrency($subscription->getPrice()?->getCurrency());
        $entity->setStatus(SubscriptionStatus::from($subscription->getStatus()));

        if ($subscription->getPrice()) {
            $price = $this->entityFactory->getPriceEntity();
            $price->setCurrency($subscription->getPrice()?->getCurrency());
            $price->setAmount($subscription->getPrice()?->getAmount());
            $price->setSchedule($subscription->getPrice()?->getSchedule());
            $entity->setPrice($price);
        }

        return $entity;
    }

    public function getSubscription(string $id): Subscription
    {
        $response = $this->sdkFactory->createSubscriptionsApi()->showSubscriptionById($id);

        return $this->buildSubscription($response);
    }
}
