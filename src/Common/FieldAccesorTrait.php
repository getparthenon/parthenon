<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
