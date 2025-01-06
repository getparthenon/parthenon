<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
