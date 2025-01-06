<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

use Parthenon\AbTesting\Decider\ChoiceDeciderInterface;

final class DeciderManager implements ChoiceDeciderInterface
{
    /**
     * @var ChoiceDeciderInterface[]
     */
    private array $deciders = [];

    public function addDecider(ChoiceDeciderInterface $decider)
    {
        $this->deciders[] = $decider;
    }

    public function getChoice(string $decisionId): ?string
    {
        foreach ($this->deciders as $decider) {
            $decision = $decider->getChoice($decisionId);

            if (!is_null($decision)) {
                return $decision;
            }
        }

        return null;
    }
}
