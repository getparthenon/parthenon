<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Action;

trait ExtractVariablesTrait
{
    protected function getVariableData($entity): array
    {
        $output = [];

        $reflectedData = new \ReflectionObject($entity);

        foreach ($reflectedData->getProperties() as $property) {
            $name = $property->getName();
            $property->setAccessible(true);
            if (!$property->isInitialized($entity)) {
                continue;
            }
            $output['entity.'.$name] = $property->getValue($entity);
        }

        return $output;
    }
}
