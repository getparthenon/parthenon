<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Invoice\Vat;

use Parthenon\Common\Address;
use Parthenon\Invoice\ItemInterface;

interface VatRuleInterface
{
    public function supports(ItemInterface $item, Address $address): bool;

    public function setVat(ItemInterface $item): void;
}
