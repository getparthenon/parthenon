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

namespace Parthenon\Billing\Subscription;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\LoggerAwareTrait;

class CachedSubscriptionProvider implements SubscriptionProviderInterface
{
    use LoggerAwareTrait;

    public const REDIS_STORAGE_SUBSCRIPTION_KEY = 'parthenon_subscription_%s';
    public const REDIS_STORAGE_SUBSCRIPTIONS_KEY = 'parthenon_subscriptions_%s';
    private array $subscriptions = [];
    private array $subscription = [];

    public function __construct(
        private SubscriptionProviderInterface $subscriptionProvider,
        private \Redis $redis,
    ) {
    }

    public function getSubscription(string $id): Subscription
    {
        if (isset($this->subscription[$id])) {
            return $this->subscription[$id];
        }

        $key = sprintf(self::REDIS_STORAGE_SUBSCRIPTION_KEY, $id);
        $rawData = $this->redis->get($key);

        if (!$rawData) {
            $this->getLogger()->debug('Fetching subscription from original subscription provider');
            $this->subscription[$id] = $this->subscriptionProvider->getSubscription($id);
            $rawData = serialize($this->subscription[$id]);
            $this->redis->set($key, $rawData, 900);
        } else {
            $this->getLogger()->debug('Got the subscription from cache');
            $this->subscription[$id] = unserialize($rawData);
        }

        return $this->subscription[$id];
    }

    public function getSubscriptionsForCustomer(CustomerInterface $customer): array
    {
        $id = (string) $customer->getId();

        if (isset($this->subscriptions[$id])) {
            return $this->subscriptions[$id];
        }

        $key = sprintf(self::REDIS_STORAGE_SUBSCRIPTIONS_KEY, $id);
        $rawData = $this->redis->get($key);

        if (!$rawData) {
            $this->getLogger()->debug('Fetching subscriptions from original subscription provider');
            $this->subscriptions[$id] = $this->subscriptionProvider->getSubscriptionsForCustomer($customer);
            $rawData = serialize($this->subscriptions[$id]);
            $this->redis->set($key, $rawData. 900);
        } else {
            $this->getLogger()->debug('Got the subscriptions from cache');
            $this->subscriptions[$id] = unserialize($rawData);
        }

        return $this->subscriptions[$id];
    }
}
