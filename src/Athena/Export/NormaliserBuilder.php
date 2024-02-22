<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Export;

use Parthenon\Export\Normaliser\NormaliserInterface;

final class NormaliserBuilder implements NormaliserBuilderInterface
{
    public function __construct(private mixed $entity, private array $fields = [])
    {
    }

    public function addField(string $fieldName, string $columnName, ?\Closure $fieldNormaliser = null): self
    {
        if (null === $fieldNormaliser) {
            $fieldNormaliser = function ($value) {
                return $value;
            };
        }

        $this->fields[] = new NormalisedField($fieldName, $columnName, $fieldNormaliser);

        return $this;
    }

    public function getNormaliser(): NormaliserInterface
    {
        if (empty($this->fields)) {
            return new DefaultNormaliser($this->entity);
        }

        return new BuiltNormaliser($this->entity, $this->fields);
    }
}
