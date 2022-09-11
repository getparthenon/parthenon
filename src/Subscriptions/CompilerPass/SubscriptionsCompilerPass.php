<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
