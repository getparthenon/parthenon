<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\ListView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Form\Type\SubscriptionPlanLimitType;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionFeatureRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SubscriptionPlanSection extends AbstractSection
{
    public function __construct(
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private SubscriptionFeatureRepositoryInterface $subscriptionFeatureRepository,
        private PriceRepositoryInterface $priceRepository,
    ) {
    }

    public function getUrlTag(): string
    {
        return 'billing-subscription-plan';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->subscriptionPlanRepository;
    }

    public function getEntity()
    {
        return new SubscriptionPlan();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Subscription Plan';
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $featureChoices = $this->subscriptionFeatureRepository->getAll();
        $prices = $this->priceRepository->getAll();

        $entityForm->section('Main')
                ->field('name', 'text')
                ->field('public', 'checkbox', ['required' => false])
                ->field('external_reference', 'text', ['required' => false])
                ->field('free', 'checkbox', ['required' => false])
                ->field('per_seat', 'checkbox', ['required' => false])
                ->field('user_count', 'text')
                ->field('has_trial', 'checkbox', ['required' => false])
                ->field('trial_length_days', 'number')
            ->end()
            ->section('Limits')
                ->field('limits', 'collection',
                    [
                        'entry_type' => SubscriptionPlanLimitType::class,
                        'entry_options' => [
                            'label' => false,
                            'choices' => $featureChoices,
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'delete_empty' => true,
                    ]
                )
            ->end()
            ->section('Features')
            ->field('features', 'collection',
                [
                    'entry_type' => ChoiceType::class,
                    'entry_options' => [
                        'label' => false,
                        'choices' => $featureChoices,
                        'choice_label' => 'name',
                        'choice_value' => 'id',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'delete_empty' => true,
                ]
            )
            ->end()
            ->section('Prices')
            ->field('prices', 'collection',
                [
                    'entry_type' => ChoiceType::class,
                    'entry_options' => [
                        'label' => false,
                        'choices' => $prices,
                        'choice_label' => 'displayName',
                        'choice_value' => 'id',
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'delete_empty' => true,
                ]
            )
            ->end();

        return $entityForm;
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('name', 'text');

        return $listView;
    }
}
