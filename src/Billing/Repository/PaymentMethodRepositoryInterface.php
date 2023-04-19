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
use Parthenon\Billing\Entity\PaymentMethod;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\RepositoryInterface;

interface PaymentMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function getPaymentMethodForCustomerAndReference(CustomerInterface $customer, string $reference): PaymentMethod;

    /**
     * @return PaymentMethod[]
     */
    public function getPaymentMethodForCustomer(CustomerInterface $customer): array;

    public function markAllCustomerMethodsAsNotDefault(CustomerInterface $customer): void;

    /**
     * @throws NoEntityFoundException
     */
    public function getDefaultPaymentMethodForCustomer(CustomerInterface $customer): PaymentMethod;
}
