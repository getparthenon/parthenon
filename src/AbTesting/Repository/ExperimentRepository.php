<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Repository;

use Parthenon\AbTesting\Entity\Experiment;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class ExperimentRepository extends DoctrineCrudRepository implements ExperimentRepositoryInterface
{
    /**
     * @return \Generator|Experiment[]
     */
    public function findAll(): \Generator
    {
        $results = $this->entityRepository->createQueryBuilder('e')->where('e.isDeleted = false')->getQuery()->toIterable();

        foreach ($results as $experiment) {
            yield $experiment;
        }
    }

    public function findByName(string $name): Experiment
    {
        $experiment = $this->entityRepository->findOneBy(['name' => $name]);

        if (!$experiment instanceof Experiment) {
            throw new NoEntityFoundException();
        }

        return $experiment;
    }
}
