<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel\Repository;

use Parthenon\Common\Repository\RepositoryInterface;

interface FunnelRepositoryInterface extends RepositoryInterface
{
    public function getUnfinishedToBeProcessed(): array;
}
