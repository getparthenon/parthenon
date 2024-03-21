<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
