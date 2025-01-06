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

namespace Parthenon\Billing\Subscriber;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\Subscription\CachedSubscriptionProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriptionChangeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private \Redis $redis,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            SubscriptionCreated::NAME => ['subscriptionCreated', 0],
            SubscriptionCancelled::NAME => ['subscriptionCancelled', 0],
        ];
    }

    public function subscriptionCreated(SubscriptionCreated $created)
    {
        $this->clearCache($created->getSubscription()->getCustomer());
    }

    public function subscriptionCancelled(SubscriptionCancelled $cancelled)
    {
        $this->clearCache($cancelled->getSubscription()->getCustomer());
    }

    private function clearCache(CustomerInterface $customer)
    {
        $this->redis->del(sprintf(CachedSubscriptionProvider::REDIS_STORAGE_SUBSCRIPTIONS_KEY, $customer->getId()));
    }
}
