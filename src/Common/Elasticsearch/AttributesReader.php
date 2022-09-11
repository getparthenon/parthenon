<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
