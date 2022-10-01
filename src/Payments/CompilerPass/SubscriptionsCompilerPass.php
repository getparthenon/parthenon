<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Payments\CompilerPass;

use Parthenon\Common\Compiler\AbstractCompilerPass;
use Parthenon\Payments\Transition\ToActiveManager;
use Parthenon\Payments\Transition\ToCancelledManager;
use Parthenon\Payments\Transition\ToOverdueManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SubscriptionsCompilerPass extends AbstractCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->handle($container, 'parthenon.payments.plan.counter_manager', 'parthenon.payments.plan.counter', 'add');
        $this->handle($container, ToActiveManager::class, 'parthenon.payments.transitions.to_active', 'addTransition');
        $this->handle($container, ToOverdueManager::class, 'parthenon.payments.transitions.to_overdue', 'addTransition');
        $this->handle($container, ToCancelledManager::class, 'parthenon.payments.transitions.to_cancelled', 'addTransition');
    }
}
