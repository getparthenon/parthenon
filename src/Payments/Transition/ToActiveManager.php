<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\Transition;

use Parthenon\Payments\Subscriber\SubscriberInterface;

final class ToActiveManager implements ToActiveManagerInterface
{
    /**
     * @var ToActiveTransitionInterface[]
     */
    private array $transitions = [];

    public function addTransition(ToActiveTransitionInterface $activeTransition): void
    {
        $this->transitions[] = $activeTransition;
    }

    public function transition(SubscriberInterface $subscriber): void
    {
        foreach ($this->transitions as $transition) {
            $transition->transition($subscriber);
        }
    }
}
