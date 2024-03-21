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
