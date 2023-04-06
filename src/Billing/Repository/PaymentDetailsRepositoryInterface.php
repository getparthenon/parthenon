<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Repository;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;

interface PaymentDetailsRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentDetailsForCustomerAndReference(CustomerInterface $customer, string $reference): PaymentDetails;

    /**
     * @return PaymentDetails[]
     */
    public function getPaymentDetailsForCustomer(CustomerInterface $customer): array;

    public function markAllCustomerDetailsAsNotDefault(CustomerInterface $customer): void;

    /**
     * @throws NoEntityFoundException
     */
    public function getDefaultPaymentDetailsForCustomer(CustomerInterface $customer): PaymentDetails;
}
