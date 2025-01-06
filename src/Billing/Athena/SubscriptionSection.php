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
