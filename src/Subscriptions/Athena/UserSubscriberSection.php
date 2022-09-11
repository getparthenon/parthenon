<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Athena;

use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Plan\Plan;
use Parthenon\Subscriptions\Plan\PlanManagerInterface;
use Parthenon\Subscriptions\Repository\SubscriberRepositoryInterface;
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
