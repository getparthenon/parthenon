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

namespace Parthenon\Billing\Event;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentCardAdded extends Event
{
    public const NAME = 'parthenon.billing.payment_card.added';

    public function __construct(
        private CustomerInterface $customer,
        private PaymentCard $paymentCard
    ) {
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

    public function getPaymentCard(): PaymentCard
    {
        return $this->paymentCard;
    }
}
