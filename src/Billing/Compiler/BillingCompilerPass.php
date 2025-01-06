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

namespace Parthenon\Billing\Compiler;

use Parthenon\Billing\Webhook\HandlerManager;
use Parthenon\Common\Compiler\AbstractCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BillingCompilerPass extends AbstractCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $this->handle($container, 'parthenon.billing.plan.counter_manager', 'parthenon.billing.plan.counter', 'add');
        $this->handle($container, HandlerManager::class, 'parthenon.billing.webhooks.handler', 'addHandler');
        $this->handle($container, \Parthenon\Billing\BillaBear\Webhook\Handler::class, 'parthenon.billing.billabear.webhooks.handler', 'add');
    }
}
