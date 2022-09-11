<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2021, all rights reserved.
 */

namespace Parthenon\DependencyInjection;

use Parthenon\DependencyInjection\Modules\AbTesting;
use Parthenon\DependencyInjection\Modules\Athena;
use Parthenon\DependencyInjection\Modules\Cloud;
use Parthenon\DependencyInjection\Modules\Common;
use Parthenon\DependencyInjection\Modules\Funnel;
use Parthenon\DependencyInjection\Modules\Health;
use Parthenon\DependencyInjection\Modules\Invoice;
use Parthenon\DependencyInjection\Modules\Notification;
use Parthenon\DependencyInjection\Modules\Payments;
use Parthenon\DependencyInjection\Modules\Subscriptions;
use Parthenon\DependencyInjection\Modules\RuleEngine;
use Parthenon\DependencyInjection\Modules\User;
use Parthenon\DependencyInjection\Modules\MultiTenancy;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('parthenon');

        $children = $treeBuilder->getRootNode()
                ->children();

        $abTesting = new AbTesting();
        $abTesting->addConfig($children);

        $athena = new Athena();
        $athena->addConfig($children);

        $common = new Common();
        $common->addConfig($children);

        $funnel = new Funnel();
        $funnel->addConfig($children);

        $health = new Health();
        $health->addConfig($children);

        $invoice = new Invoice();
        $invoice->addConfig($children);

        $notifcation = new Notification();
        $notifcation->addConfig($children);

        $payments = new Payments();
        $payments->addConfig($children);

        $plan = new Subscriptions();
        $plan->addConfig($children);

        $user = new User();
        $user->addConfig($children);
        
        $multiTenancy = new MultiTenancy();
        $multiTenancy->addConfig($children);

        $cloud = new Cloud();
        $cloud->addConfig($children);

        $children->end();

        return $treeBuilder;
    }
}
