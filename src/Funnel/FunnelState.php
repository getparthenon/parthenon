<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel;

final class FunnelState
{
    private $entity;

    private int $step;

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getStep(): int
    {
        return $this->step;
    }

    public function setStep($step): self
    {
        $this->step = $step;

        return $this;
    }
}
