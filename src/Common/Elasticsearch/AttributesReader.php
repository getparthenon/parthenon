<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Elasticsearch;

use Parthenon\Common\Exception\Elasticsearch\InvalidAttibuteException;

class AttributesReader
{
    public function getAttributes(object $object)
    {
        $output = [];

        $reflectedObject = new \ReflectionObject($object);
        $propertries = $reflectedObject->getProperties();

        foreach ($propertries as $propertry) {
            foreach ($propertry->getAttributes() as $attribute) {
                try {
                    $output[] = [
                        'propertyName' => $propertry->getName(),
                        'instance' => $attribute->newInstance(),
                    ];
                } catch (\Throwable $throwable) {
                    throw new InvalidAttibuteException(sprintf('Invalid attribute on %s property. Message: %e', $propertry->getName(), $throwable->getMessage()), $throwable->getCode(), $throwable);
                }
            }
        }

        return $output;
    }
}
