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

use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ReadView;
use Parthenon\Billing\Entity\EmbeddedSubscription;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\User\Athena\UserSection;
use Parthenon\User\Repository\UserRepositoryInterface;

class CustomerUserSection extends UserSection
{
    public function __construct(private CustomerRepositoryInterface|UserRepositoryInterface $customerRepository, private PlanManagerInterface $planManager)
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
