<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Experiment;

use Parthenon\User\Entity\UserInterface;

interface ResultLoggerInterface
{
    public function log(string $resultId, ?UserInterface $user = null, array $userAttributes = [], array $eventTags = []): void;
}
