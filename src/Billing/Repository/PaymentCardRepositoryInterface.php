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

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;

interface PaymentCardRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentCardForCustomerAndReference(CustomerInterface $customer, string $reference): PaymentCard;

    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentCardForReference(string $reference): PaymentCard;

    /**
     * @return PaymentCard[]
     */
    public function getPaymentCardForCustomer(CustomerInterface $customer): array;

    public function markAllCustomerCardsAsNotDefault(CustomerInterface $customer): void;

    /**
     * @throws NoEntityFoundException
     */
    public function getDefaultPaymentCardForCustomer(CustomerInterface $customer): PaymentCard;
}
