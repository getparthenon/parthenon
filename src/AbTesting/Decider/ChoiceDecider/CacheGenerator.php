<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
