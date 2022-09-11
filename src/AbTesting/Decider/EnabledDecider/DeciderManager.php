<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
