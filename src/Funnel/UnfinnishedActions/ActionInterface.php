<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel\UnfinnishedActions;

interface ActionInterface
{
    public function process($entity);

    public function supports($entity): bool;
}
