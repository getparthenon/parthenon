<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Experiment;

use Optimizely\Optimizely;
use Parthenon\User\Entity\UserInterface;

final class OptimizelyDecider implements DeciderInterface
{
    public function __construct(private Optimizely $optimizely)
    {
    }

    public function doExperiment(string $experimentName, ?UserInterface $user = null, array $options = []): string
    {
        $userId = ($user) ? $user->getId() : null;

        return $this->optimizely->activate($experimentName, $userId, $options);
    }
}
