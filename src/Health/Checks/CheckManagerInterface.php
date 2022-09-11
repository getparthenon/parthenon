<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Health\Checks;

interface CheckManagerInterface
{
    public function addCheck(CheckInterface $check): self;

    /**
     * @return CheckInterface[]
     */
    public function getChecks(): array;
}
