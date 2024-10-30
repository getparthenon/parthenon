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

namespace Parthenon\Billing\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;

interface SubscriptionRepositoryInterface extends CrudRepositoryInterface
{
    public function getActiveSubscriptionCount(CustomerInterface $customer): int;

    public function hasActiveSubscription(CustomerInterface $customer): bool;

    /**
     * @throws NoEntityFoundException
     */
    public function getOneActiveSubscriptionForCustomer(CustomerInterface $customer): Subscription;

    /**
     * @return Subscription[]
     */
    public function getAllForCustomer(CustomerInterface $customer): array;

    /**
     * @return Subscription[]
     */
    public function getAllActiveForCustomer(CustomerInterface $customer): array;

    public function updateValidUntilForAllActiveSubscriptions(CustomerInterface $customer, string $mainExternalReference, \DateTimeInterface $validUntil): void;

    /**
     * @return Subscription[]
     */
    public function getForPayment(Payment $payment): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getForMainAndChildExternalReference(string $mainExternalReference, string $childExternalReference): Subscription;
}
