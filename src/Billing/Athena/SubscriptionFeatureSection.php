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
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\SubscriptionFeature;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;

class SubscriptionFeatureSection extends AbstractSection
{
    public function __construct(private SubscriptionFeatureRepositoryInterface $featureRepository)
    {
    }

    public function getUrlTag(): string
    {
        return 'billing-subscription-features';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->featureRepository;
    }

    public function getEntity()
    {
        return new SubscriptionFeature();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'EmbeddedSubscription Features';
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView->section('Main')
                ->field('name')
                ->field('code')
                ->field('description')
            ->end();

        return $readView;
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $entityForm->section('Main')
                ->field('code')
                ->field('name')
                ->field('description')
            ->end();

        return $entityForm;
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('name', 'text')
            ->addField('code', 'text')
            ->addField('description', 'text');

        return $listView;
    }
}
