<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Invoice\Vat;

use Parthenon\Common\Address;
use Parthenon\Invoice\ItemInterface;

interface VatRuleInterface
{
    public function supports(ItemInterface $item, Address $address): bool;

    public function setVat(ItemInterface $item);
}
