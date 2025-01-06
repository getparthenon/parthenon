<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\AbTesting\Decider\ChoiceDecider;

use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;

final class CacheGenerator
{
    public const REDIS_KEY = 'abtesting_decision_cache';

    private \Redis $redis;
    private ExperimentRepositoryInterface $experimentRepository;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, \Redis $redis)
    {
        $this->experimentRepository = $experimentRepository;
        $this->redis = $redis;
    }

    public function generate(): void
    {
        $cache = [];

        foreach ($this->experimentRepository->findAll() as $experiment) {
            foreach ($experiment->getVariants() as $variant) {
                if ($variant->isIsDefault()) {
                    $cache[$experiment->getName()] = $variant->getName();
                }
            }
        }

        $this->redis->set(self::REDIS_KEY, json_encode($cache));
    }
}
