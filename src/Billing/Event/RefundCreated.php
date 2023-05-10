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

namespace App\Parthenon\Billing\Event;

use Parthenon\Billing\Entity\Refund;
use Symfony\Contracts\EventDispatcher\Event;

class RefundCreated extends Event
{
    public const NAME = 'parthenon.billing.refund.created';

    public function __construct(private Refund $refund)
    {
    }

    public function getRefund(): Refund
    {
        return $this->refund;
    }

    public function setRefund(Refund $refund): void
    {
        $this->refund = $refund;
    }
}
