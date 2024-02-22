<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ParthenonExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        if (empty($config)) {
            return;
        }

        $this->handleCommon($config, $container);
        $this->handleNotification($config, $container);
        $this->handleUserConfig($config, $container);
        $this->handleAthena($config, $container);
        $this->handleAbTesting($config, $container);
        $this->handleFunnelConfig($config, $container);
        $this->handleHealth($config, $container);
        $this->handleInvoice($config, $container);
        $this->handlePayments($config, $container);
        $this->handleMultiTenancy($config, $container);
        $this->handleCloud($config, $container);
        $this->handleExport($config, $container);
        $this->handleBilling($config, $container);
    }

    public function handleFunnelConfig(array $config, ContainerBuilder $container)
    {
        $funnel = new Funnel();
        $funnel->handleDefaultParameters($container);
        $funnel->handleConfiguration($config, $container);
    }

    public function handleUserConfig(array $config, ContainerBuilder $container)
    {
        $user = new User();
        $user->handleDefaultParameters($container);
        $user->handleConfiguration($config, $container);
    }

    public function handleCommon(array $config, ContainerBuilder $container)
    {
        $common = new Common();
        $common->handleDefaultParameters($container);
        $common->handleConfiguration($config, $container);
    }

    public function handleNotification(array $config, ContainerBuilder $container)
    {
        $notification = new Notification();
        $notification->handleDefaultParameters($container);
        $notification->handleConfiguration($config, $container);
    }

    public function handleAthena(array $config, ContainerBuilder $container)
    {
        $athena = new Athena();
        $athena->handleDefaultParameters($container);
        $athena->handleConfiguration($config, $container);
    }

    public function handleHealth(array $config, ContainerBuilder $container)
    {
        $health = new Health();
        $health->handleDefaultParameters($container);
        $health->handleConfiguration($config, $container);
    }

    public function handleInvoice(array $config, ContainerBuilder $container)
    {
        $invoice = new Invoice();
        $invoice->handleDefaultParameters($container);
        $invoice->handleConfiguration($config, $container);
    }

    public function handleAbTesting(array $config, ContainerBuilder $container)
    {
        $abTesting = new AbTesting();
        $abTesting->handleDefaultParameters($container);
        $abTesting->handleConfiguration($config, $container);
    }

    public function handleBilling(array $config, ContainerBuilder $container)
    {
        $export = new Billing();
        $export->handleDefaultParameters($container);
        $export->handleConfiguration($config, $container);
    }

    public function handleExport(array $config, ContainerBuilder $container)
    {
        $export = new Export();
        $export->handleDefaultParameters($container);
        $export->handleConfiguration($config, $container);
    }

    private function handlePayments(array $config, ContainerBuilder $container)
    {
        $payments = new Payments();
        $payments->handleDefaultParameters($container);
        $payments->handleConfiguration($config, $container);
    }

    private function handleMultiTenancy(array $config, ContainerBuilder $container)
    {
        $multiTenancy = new MultiTenancy();
        $multiTenancy->handleDefaultParameters($container);
        $multiTenancy->handleConfiguration($config, $container);
    }

    private function handleCloud(array $config, ContainerBuilder $container)
    {
        $cloud = new Cloud();
        $cloud->handleDefaultParameters($container);
        $cloud->handleConfiguration($config, $container);
    }
}
