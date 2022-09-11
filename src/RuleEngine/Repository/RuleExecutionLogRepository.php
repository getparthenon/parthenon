<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Repository;

use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\Repository\DoctrineRepository;
use Parthenon\RuleEngine\Entity\RuleExecutionLog;

final class RuleExecutionLogRepository extends DoctrineRepository implements RuleExecutionLogRepositoryInterface
{
    public function findLastForEntityAndField(string $entity, string $field): ?RuleExecutionLog
    {
        try {
            /** @var RuleExecutionLog $log */
            $log = $this->entityRepository->findOneBy(['entityName' => $entity, 'fieldName' => $field], ['createdAt' => 'DESC']);

            return $log;
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
