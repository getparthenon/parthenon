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

namespace Parthenon\Common;

use Parthenon\Common\Exception\InvalidFieldException;

trait FieldAccesorTrait
{
    protected function getFieldData($data, $fieldName)
    {
        if (false !== strpos($fieldName, '.')) {
            [$field, $subfield] = explode('.', $fieldName, 2);

            return $this->getFieldData($this->getFieldData($data, $field), $subfield);
        }
        $camelCase = str_replace('_', '', ucwords($fieldName, '_'));
        $getter = 'get'.$camelCase;
        $isser = 'is'.$camelCase;
        $hasser = 'has'.$camelCase;
        $camelCaseAccessor = lcfirst($camelCase);
        if (is_array($data) && array_key_exists($fieldName, $data)) {
            return $data[$fieldName];
        }

        if (is_object($data) && method_exists($data, $fieldName)) {
            return $data->$fieldName();
        }
        if (is_object($data) && method_exists($data, $camelCaseAccessor)) {
            return $data->$camelCaseAccessor();
        }

        if (is_object($data) && method_exists($data, $getter)) {
            return $data->$getter();
        }

        if (is_object($data) && method_exists($data, $isser)) {
            return $data->$isser();
        }

        if (is_object($data) && method_exists($data, $hasser)) {
            return $data->$hasser();
        }

        if (is_object($data) && property_exists($data, $camelCaseAccessor)) {
            return $data->$camelCaseAccessor;
        }

        if (is_object($data) && property_exists($data, $fieldName)) {
            return $data->$fieldName;
        }

        throw new InvalidFieldException('The field '.$fieldName.' is invalid');
    }
}
