<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Repository;

use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Common\Exception\NoEntityFoundException;

interface PaymentRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return Payment[]
     */
    public function getPaymentsForCustomerDuring(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentForReference(string $reference): Payment;

    /**
     * @return Payment[]
     */
    public function getPaymentsForCustomer(CustomerInterface $customer): array;

    /**
     * @return Payment[]
     */
    public function getPaymentsForSubscription(Subscription $subscription): array;

    /**
     * @throws NoEntityFoundException
     */
    public function getLastPaymentForSubscription(Subscription $subscription): Payment;

    /**
     * @throws NoEntityFoundException
     */
    public function getLastPaymentForCustomer(CustomerInterface $customer): Payment;
}
