<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Repository;

use Parthenon\Common\Repository\RepositoryInterface;
use Parthenon\RuleEngine\Entity\RuleExecutionLog;

interface RuleExecutionLogRepositoryInterface extends RepositoryInterface
{
    public function findLastForEntityAndField(string $entity, string $field): ?RuleExecutionLog;
}
