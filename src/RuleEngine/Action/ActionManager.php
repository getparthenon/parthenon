<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Action;

use Parthenon\RuleEngine\Exception\NoActionFoundException;

final class ActionManager
{
    /**
     * @var ActionInterface[]
     */
    private array $actions = [];

    public function addAction(ActionInterface $action): self
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function getActionInfo(): array
    {
        $actions = [];
        $actionOptions = [];
        foreach ($this->actions as $action) {
            $className = get_class($action);
            $actions[$action->getName()] = $className;
            $actionOptions[$className] = $action->getOptions();
        }

        return [$actions, $actionOptions];
    }

    public function getAction($className): ActionInterface
    {
        foreach ($this->actions as $action) {
            if (is_a($action, $className, true)) {
                return $action;
            }
        }
        throw new NoActionFoundException();
    }
}
