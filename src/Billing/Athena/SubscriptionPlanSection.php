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
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Factory\EntityFactoryInterface;
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
        private EntityFactoryInterface $entityFactory,
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
        return $this->entityFactory->getSubscriptionPlanEntity();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'EmbeddedSubscription Plan';
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
