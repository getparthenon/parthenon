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
