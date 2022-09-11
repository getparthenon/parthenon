<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel\UnfinnishedActions;

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
}
