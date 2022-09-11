<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Repository;

use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\RuleEngine\Entity\Rule;

final class RuleEngineRepository extends DoctrineRepository implements RuleRepositoryInterface
{
    public function getEntity()
    {
        return new Rule();
    }

    public function getAllRulesForEntity(string $entity): array
    {
        return $this->entityRepository->findBy(['entity' => $entity]);
    }
}
