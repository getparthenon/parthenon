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

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\Button;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\Settings;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;

class SubscriptionSection extends AbstractSection
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('customer', 'text')
            ->addField('planName', 'text')
            ->addField('status', 'text')
            ->addField('validUntil', 'text');

        return $listView;
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView->section('Main')
                ->field('planName')
                ->field('createdAt')
                ->field('validUntil')
            ->end();

        return $readView;
    }

    public function getButtons(): array
    {
        return [
            new Button('subscription_cancel', 'Cancel Subscription', 'parthenon_billing_athena_subscription_cancel'),
            new Button('subscription_cancel_refund', 'Cancel and Refund Subscription', 'parthenon_billing_athena_subscription_cancel_refund'),
        ];
    }

    public function getSettings(): Settings
    {
        return new Settings(['create' => false, 'edit' => false]);
    }

    public function getUrlTag(): string
    {
        return 'subscriptions';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->subscriptionRepository;
    }

    public function getEntity()
    {
        return $this->entityFactory->getSubscriptionEntity();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Subscriptions';
    }
}
