<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Export;

use Parthenon\Common\Exception\InvalidFieldException;
use Parthenon\Common\FieldAccesorTrait;
use Parthenon\Export\Exception\InvalidDataForNormaliserException;
use Parthenon\Export\Normaliser\NormaliserInterface;

final class DefaultNormaliser implements NormaliserInterface
{
    use FieldAccesorTrait;

    public function __construct(private string $className)
    {
    }

    public function supports(mixed $item): bool
    {
        return is_a($item, $this->className);
    }

    public function normalise(mixed $item): array
    {
        if (!is_object($item)) {
            throw new InvalidDataForNormaliserException();
        }

        $reflectionObject = new \ReflectionObject($item);
        $output = [];
        foreach ($reflectionObject->getProperties() as $attribute) {
            $key = $attribute->getName();
            $value = $this->getFieldData($item, $key);
            if (!is_object($value)) {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $output[$key] = $value;
            } else {
                if ($value instanceof \DateTimeInterface) {
                    $output[$key] = $value->format(DATE_ATOM);
                    continue;
                }

                if (method_exists($value, '__toString')) {
                    $output[$key] = (string) $value;
                    continue;
                }

                try {
                    $value = $this->getFieldData($value, 'id');
                    $key = $key.'_id';
                    $output[$key] = $value;
                } catch (InvalidFieldException $e) {
                }
            }
        }

        return $output;
    }
}
