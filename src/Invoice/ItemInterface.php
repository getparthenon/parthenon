<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Invoice;

use Brick\Money\Money;

interface ItemInterface
{
    public function setVatRate(float $vatRate);

    public function getSubTotal(): Money;

    public function getVat(): Money;

    public function getSingleVat(): Money;

    public function getTotal(): Money;

    public function getName(): string;

    public function getType(): string;
}
