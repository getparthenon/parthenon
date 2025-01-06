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

namespace Parthenon\Payments\Athena;

use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Plan\Plan;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\Payments\Repository\SubscriberRepositoryInterface;
use Parthenon\User\Athena\TeamSection;
use Parthenon\User\Repository\TeamRepositoryInterface;

class TeamSubscriberSection extends TeamSection
{
    public function __construct(private SubscriberRepositoryInterface|TeamRepositoryInterface $subscriberRepository, private PlanManagerInterface $planManager)
    {
        parent::__construct($this->subscriberRepository);
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView = parent::buildListView($listView);
        $listView->addField('identifier', 'text', link: true)
            ->addField('subscription.planName', 'text')
            ->addField('subscription.status', 'text')
            ->addField('subscription.validUntil', 'text');

        return $listView;
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $entityForm = parent::buildEntityForm($entityForm);

        $planNames = array_map(function (Plan $plan) {
            return $plan->getName();
        }, $this->planManager->getPlans());

        $entityForm->section('plan')
            ->field('subscription.planName', 'choice', ['choices' => array_combine($planNames, $planNames)])
            ->field('subscription.status', 'choice', ['choices' => array_combine(Subscription::STATUS_ARRAY, Subscription::STATUS_ARRAY)])
            ->field('subscription.validUntil', 'date')
        ->end();

        return $entityForm;
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView = parent::buildReadView($readView);

        $readView->section('plan')
            ->field('subscription.planName')
            ->field('subscription.validUntil')
            ->field('subscription.status')
            ->end();

        return $readView;
    }
}
