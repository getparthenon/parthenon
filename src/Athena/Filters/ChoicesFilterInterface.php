<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Filters;

interface ChoicesFilterInterface
{
    public function getChoices(): array;

    public function setChoices(array $choices): self;
}
