<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
