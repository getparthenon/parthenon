<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
