<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Processor;

use Parthenon\RuleEngine\Exception\InvalidEntityException;
use Parthenon\RuleEngine\Executor;
use Parthenon\RuleEngine\Repository\RuleRepositoryInterface;

final class InstantProcessor implements ProcessorInterface
{
    private RuleRepositoryInterface $ruleRepository;

    private Executor $executor;

    public function __construct(RuleRepositoryInterface $ruleRepository, Executor $executor)
    {
        $this->ruleRepository = $ruleRepository;
        $this->executor = $executor;
    }

    public function process($entity)
    {
        if (!is_object($entity)) {
            throw new InvalidEntityException(var_export($entity, true).' is not a valid entity');
        }

        $className = get_class($entity);

        $rules = $this->ruleRepository->getAllRulesForEntity($className);

        foreach ($rules as $rule) {
            $this->executor->execute($rule, $entity);
        }
    }
}
