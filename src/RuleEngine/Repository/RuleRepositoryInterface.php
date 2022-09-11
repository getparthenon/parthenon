<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Repository;

use Parthenon\Common\Repository\RepositoryInterface;

interface RuleRepositoryInterface extends RepositoryInterface
{
    public function getAllRulesForEntity(string $entity): array;
}
