<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Invoice;

use Parthenon\Common\Address;
use Parthenon\Invoice\Vat\VatRuleInterface;

final class CountryRules
{
    /**
     * @var VatRuleInterface[]
     */
    private array $rules = [];

    public function addRule(VatRuleInterface $vatRule): self
    {
        $this->rules[] = $vatRule;

        return $this;
    }

    public function handleRules(ItemInterface $item, Address $address)
    {
        foreach ($this->rules as $rule) {
            if ($rule->supports($item, $address)) {
                $rule->setVat($item);
            }
        }
    }
}
