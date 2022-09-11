<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel\Repository;

final class RepositoryManager
{
    /**
     * @var FunnelRepositoryInterface[]
     */
    private array $repositories = [];

    public function addRepository(FunnelRepositoryInterface $repository): self
    {
        $this->repositories[] = $repository;

        return $this;
    }

    /**
     * @return FunnelRepositoryInterface[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }
}
