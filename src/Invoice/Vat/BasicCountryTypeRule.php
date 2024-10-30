<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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

    public function setVat(ItemInterface $item): void
    {
        $item->setVatRate($this->percentage);
    }
}
