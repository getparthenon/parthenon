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

namespace Parthenon\Billing\Event;

use Parthenon\Billing\Entity\ChargeBack;
use Symfony\Contracts\EventDispatcher\Event;

class ChargeBackCreated extends Event
{
    public const NAME = 'parthenon.billing.charge_back.created';

    public function __construct(private ChargeBack $chargeBack)
    {
    }

    public function getChargeBack(): ChargeBack
    {
        return $this->chargeBack;
    }
}
