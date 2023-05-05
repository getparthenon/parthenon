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

namespace Parthenon\Billing\Subscription;

use Obol\Model\Events\AbstractCharge;
use Obol\Model\PaymentDetails;
use Parthenon\Billing\Entity\Payment;

interface PaymentEventLinkerInterface
{
    public function linkPaymentDetailsToSubscription(Payment $payment, PaymentDetails $charge): void;

    public function linkToSubscription(Payment $payment, AbstractCharge $charge): void;
}
