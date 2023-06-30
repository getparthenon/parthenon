<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Export;

use Parthenon\Common\FieldAccesorTrait;
use Parthenon\Export\Normaliser\NormaliserInterface;

class BuiltNormaliser implements NormaliserInterface
{
    use FieldAccesorTrait;

    /**
     * BuiltNormaliser constructor.
     *
     * @param NormalisedField[] $fields
     */
    public function __construct(private string $className, private array $fields)
    {
    }

    public function supports(mixed $item): bool
    {
        return is_a($item, $this->className);
    }

    public function normalise(mixed $item): array
    {
        $output = [];
        foreach ($this->fields as $field) {
            $fieldName = $field->getFieldName();
            $columnName = $field->getColumnName();
            $normaliserMethod = $field->getNormaliserMethod();

            $value = $this->getFieldData($item, $fieldName);

            $output[$columnName] = $normaliserMethod($value);
        }

        return $output;
    }
}
