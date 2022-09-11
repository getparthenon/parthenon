<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Invoice\Vat;

use Parthenon\Common\Address;
use Parthenon\Invoice\ItemInterface;

final class BasicCountryTypeRule implements VatRuleInterface
{
    private string $country;
    private string $type;
    private float $percentage;

    public function __construct(string $country, string $type, float $percentage)
    {
        $this->country = $country;
        $this->type = $type;
        $this->percentage = $percentage;
    }

    public function supports(ItemInterface $item, Address $address): bool
    {
        return $item->getType() === $this->type && $this->country === $address->getCountry();
    }

    public function setVat(ItemInterface $item)
    {
        $item->setVatRate($this->percentage);
    }
}
