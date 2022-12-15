<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Decider\ChoiceDecider;

use Parthenon\AbTesting\Decider\ChoiceDeciderInterface;

final class PredefinedChoice implements ChoiceDeciderInterface
{
    private \Redis $redis;
    private array $choices;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getChoice(string $decisionId): ?string
    {
        if (!isset($this->choices)) {
            $cache = $this->redis->get(CacheGenerator::REDIS_KEY);

            if (is_null($cache)) { // @phpstan-ignore-line
                $this->choices = [];

                return null;
            }

            $choicesArray = json_decode($cache, true);

            if (is_null($choicesArray)) {
                $this->choices = [];

                return null;
            }

            $this->choices = $choicesArray;
        }

        if (!isset($this->choices[$decisionId])) {
            return null;
        }

        if ('control' !== $this->choices[$decisionId] && 'experiment' !== $this->choices[$decisionId]) {
            return null;
        }

        return $this->choices[$decisionId];
    }
}
