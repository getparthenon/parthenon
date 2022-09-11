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

namespace Parthenon\Subscriptions\CompilerPass;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Parthenon\Subscriptions\Transition\ToActiveManager;
use Parthenon\Subscriptions\Transition\ToCancelledManager;
use Parthenon\Subscriptions\Transition\ToOverdueManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SubscriptionsCompilerPass extends AbstractCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handle($container, 'parthenon.subscriptions.plan.counter_manager', 'parthenon.subscriptions.plan.counter', 'add');
        $this->handle($container, ToActiveManager::class, 'parthenon.subscriptions.transitions.to_active', 'addTransition');
        $this->handle($container, ToOverdueManager::class, 'parthenon.subscriptions.transitions.to_overdue', 'addTransition');
        $this->handle($container, ToCancelledManager::class, 'parthenon.subscriptions.transitions.to_cancelled', 'addTransition');
    }
}
