<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
    public function getConfigTreeBuilder()
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
