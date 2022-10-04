<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
