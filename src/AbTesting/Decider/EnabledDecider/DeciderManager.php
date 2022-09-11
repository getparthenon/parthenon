<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Decider\EnabledDecider;

use Parthenon\AbTesting\Decider\EnabledDeciderInterface;

final class DeciderManager implements DecidedManagerInterface
{
    /**
     * @var EnabledDeciderInterface[]
     */
    private array $deciders = [];

    private bool $enabled;

    public function add(EnabledDeciderInterface $enabledDecider): void
    {
        $this->deciders[] = $enabledDecider;
    }

    public function isTestable(): bool
    {
        if (isset($this->enabled)) {
            return $this->enabled;
        }

        foreach ($this->deciders as $enabledDecider) {
            if (!$enabledDecider->isTestable()) {
                $this->enabled = false;

                return false;
            }
        }

        $this->enabled = true;

        return true;
    }
}
