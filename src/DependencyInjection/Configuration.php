<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\DependencyInjection;

use Parthenon\DependencyInjection\Modules\AbTesting;
use Parthenon\DependencyInjection\Modules\Athena;
use Parthenon\DependencyInjection\Modules\Billing;
use Parthenon\DependencyInjection\Modules\Cloud;
use Parthenon\DependencyInjection\Modules\Common;
use Parthenon\DependencyInjection\Modules\Export;
use Parthenon\DependencyInjection\Modules\Funnel;
use Parthenon\DependencyInjection\Modules\Health;
use Parthenon\DependencyInjection\Modules\Invoice;
use Parthenon\DependencyInjection\Modules\MultiTenancy;
use Parthenon\DependencyInjection\Modules\Notification;
use Parthenon\DependencyInjection\Modules\Payments;
use Parthenon\DependencyInjection\Modules\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('parthenon');

        $children = $treeBuilder->getRootNode()
                ->children();

        $athena = new Athena();
        $athena->addConfig($children);

        $abTesting = new AbTesting();
        $abTesting->addConfig($children);

        $common = new Common();
        $common->addConfig($children);

        $user = new User();
        $user->addConfig($children);

        $notifcation = new Notification();
        $notifcation->addConfig($children);

        $payments = new Payments();
        $payments->addConfig($children);

        $funnel = new Funnel();
        $funnel->addConfig($children);

        $health = new Health();
        $health->addConfig($children);

        $invoice = new Invoice();
        $invoice->addConfig($children);

        $multiTenancy = new MultiTenancy();
        $multiTenancy->addConfig($children);

        $cloud = new Cloud();
        $cloud->addConfig($children);

        $export = new Export();
        $export->addConfig($children);

        $billing = new Billing();
        $billing->addConfig($children);

        $children->end();

        return $treeBuilder;
    }
}
