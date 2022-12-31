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

namespace Parthenon\Invoice;

use Brick\Money\Context\AutoContext;
use Brick\Money\Money;

final class Item implements ItemInterface
{
    private string $name;
    private Money $money;
    private int $quantity;
    private float $vat;
    private string $type;
    private string $description;
    private string $currency;

    public function __construct(string $name, string $description, Money $money, int $quantity, float $vat, string $currency)
    {
        $this->name = $name;
        $this->money = $money;
        $this->quantity = $quantity;
        $this->vat = $vat;
        $this->description = $description;
        $this->currency = $currency;
    }

    public function getSubTotal(): Money
    {
        return $this->money->multipliedBy($this->quantity);
    }

    public function getVat(): Money
    {
        return $this->getSingleVat()->multipliedBy($this->quantity);
    }

    public function getSingleVat(): Money
    {
        if (0.0 == $this->vat) {
            return Money::zero($this->currency, new AutoContext());
        }

        $multiplier = $this->vat / 100;

        return $this->money->to(new AutoContext())->multipliedBy($multiplier);
    }

    public function getTotal(): Money
    {
        return $this->getSubTotal()->plus($this->getVat());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setVatRate(float $vatRate)
    {
        $this->vat = $vatRate;
    }

    public function getType(): string
    {
        if (!isset($this->type)) {
            return '';
        }

        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
