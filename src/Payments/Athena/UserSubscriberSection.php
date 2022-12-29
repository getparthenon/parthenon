<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
use Parthenon\User\Repository\UserRepositoryInterface;

class UserSubscriberSection extends TeamSection
{
    public function __construct(private SubscriberRepositoryInterface|UserRepositoryInterface $subscriberRepository, private PlanManagerInterface $planManager)
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
