<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine;

use Parthenon\Common\FieldAccesorTrait;
use Parthenon\RuleEngine\Action\ActionManager;
use Parthenon\RuleEngine\Entity\Rule;
use Parthenon\RuleEngine\Entity\RuleExecutionLog;
use Parthenon\RuleEngine\Repository\RuleExecutionLogRepositoryInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Executor
{
    use FieldAccesorTrait;

    private ActionManager $actionManager;

    private ExpressionLanguage $expressionLanguage;

    private RuleExecutionLogRepositoryInterface $ruleExecutionLogRepository;

    public function __construct(ActionManager $actionManager, RuleExecutionLogRepositoryInterface $ruleExecutionLogRepository)
    {
        $this->actionManager = $actionManager;
        $this->expressionLanguage = new ExpressionLanguage();
        $this->ruleExecutionLogRepository = $ruleExecutionLogRepository;
    }

    public function execute(Rule $rule, $entity): void
    {
        $entityValue = $this->getFieldData($entity, $rule->getField());
        $value = $this->expressionLanguage->evaluate('field '.$rule->getComparison().' "'.$rule->getValue().'"', ['field' => $entityValue]);
        if (true === $value) {
            $className = get_class($entity);
            $log = $this->ruleExecutionLogRepository->findLastForEntityAndField($className, $rule->getField());

            if ($log) {
                if ($log->getValue() === $entityValue) {
                    return;
                }
            }

            $action = $this->actionManager->getAction($rule->getAction());
            $action->execute($rule->getOptions(), $entity);

            $log = new RuleExecutionLog();
            $log->setEntityName($className)->setFieldName($rule->getField())->setEntityId($entity->getId())->setValue($entityValue)->setCreatedAt(new \DateTime('now'));
            $this->ruleExecutionLogRepository->save($log);
        }
    }
}
