<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health\Checks;

final class CheckManager
{
    /**
     * @var CheckInterface[]
     */
    private array $checks = [];

    public function addCheck(CheckInterface $check): self
    {
        $this->checks[] = $check;

        return $this;
    }

    /**
     * @return CheckInterface[]
     */
    public function getChecks(): array
    {
        return $this->checks;
    }
}
