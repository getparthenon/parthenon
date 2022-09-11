<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Decider;

interface ChoiceDeciderInterface
{
    public function getChoice(string $decisionId): ?string;
}
