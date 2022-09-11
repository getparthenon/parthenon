<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
