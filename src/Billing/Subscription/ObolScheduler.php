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

namespace Parthenon\Billing\Subscription;

use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\Subscription;

class ObolScheduler implements SchedulerInterface
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    public function scheduleNextCharge(Subscription $subscription): void
    {
        $obolSubscription = $this->provider->subscriptions()->get($subscription->getMainExternalReference(), $subscription->getChildExternalReference());
        $subscription->setValidUntil($obolSubscription->getValidUntil());
    }
}
