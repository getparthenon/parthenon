<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine;

use Parthenon\RuleEngine\Repository\RuleEngineRepositoryInterface;

final class RepositoryManager
{
    private array $repositories = [];

    public function addRepository(RuleEngineRepositoryInterface $repository): self
    {
        $this->repositories[] = $repository;

        return $this;
    }

    /**
     * @return RuleEngineRepositoryInterface[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getEntityInfo(): array
    {
        $entities = [];
        $entityProperties = [];
        foreach ($this->repositories as $repository) {
            $entity = $repository->getEntity();

            if (!$entity || !is_object($entity)) {
                throw new \Exception('Invalid entity returned');
            }
            $entityName = get_class($entity);
            $entities[] = $entityName;
            $reflectedClass = new \ReflectionClass($entity);

            $properties = [];
            foreach ($reflectedClass->getProperties() as $property) {
                $properties[] = $property->getName();
            }

            $entityProperties[$entityName] = $properties;
        }

        return [$entities, $entityProperties];
    }
}
