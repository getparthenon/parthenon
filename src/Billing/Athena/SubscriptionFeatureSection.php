<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
