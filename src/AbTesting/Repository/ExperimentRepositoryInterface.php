<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

interface ExperimentRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * @return \Generator|Experiment[]
     */
    public function findAll(): \Generator;

    /**
     * @throws NoEntityFoundException
     */
    public function findByName(string $name): Experiment;
}
