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

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ReadView;
use Parthenon\Billing\Entity\EmbeddedSubscription;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\User\Athena\TeamSection;
use Parthenon\User\Repository\TeamRepositoryInterface;

class CustomerTeamSection extends TeamSection
{
    public function __construct(private CustomerRepositoryInterface|TeamRepositoryInterface $customerRepository, private PlanManagerInterface $planManager)
    {
        parent::__construct($this->customerRepository);
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $entityForm = parent::buildEntityForm($entityForm);

        $planNames = array_map(function (Plan $plan) {
            return $plan->getName();
        }, $this->planManager->getPlans());

        $entityForm->section('plan')
            ->field('subscription.planName', 'choice', ['choices' => array_combine($planNames, $planNames)])
            ->field('subscription.status', 'choice', ['choices' => array_combine(EmbeddedSubscription::STATUS_ARRAY, EmbeddedSubscription::STATUS_ARRAY)])
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
