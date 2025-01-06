<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\Billing\Tax;

use Parthenon\Common\Address;

class CountryRules implements CountryRulesInterface
{
    protected array $rates = [
        // EU
        'AT' => 20,
        'BE' => 21,
        'BG' => 20,
        'HR' => 25,
        'CY' => 19,
        'CZ' => 21,
        'DK' => 25,
        'EE' => 20,
        'FI' => 24,
        'FR' => 20,
        'DE' => 19,
        'GR' => 24,
        'HU' => 27,
        'IE' => 23,
        'IT' => 22,
        'LV' => 21,
        'LT' => 21,
        'LU' => 17,
        'MT' => 18,
        'NL' => 21,
        'PL' => 23,
        'PT' => 23,
        'RO' => 19,
        'SK' => 20,
        'SI' => 22,
        'ES' => 21,
        'SE' => 25,

        'IS' => 24,

        // UK
        'GB' => 20,

        // Swiss
        'CH' => 7.7,

        // North America
        'US' => 0,
        'CA' => 5,
        'MX' => 16,

        // Down under
        'AU' => 18,
        'NZ' => 15,

        // Russia
        'RU' => 20,
        'CN' => 13,
    ];

    public function getDigitalVatPercentage(Address $address): int|float
    {
        if (!isset($this->rates[$address->getCountry()])) {
            return 0;
        }

        return $this->rates[$address->getCountry()];
    }
}
