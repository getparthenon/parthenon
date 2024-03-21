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
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Obol\PriceRegisterInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class PriceSection extends AbstractSection
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
        private PriceRegisterInterface $priceRegister,
        private ProductRepositoryInterface $productRepository,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function getUrlTag(): string
    {
        return 'billing-price';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->priceRepository;
    }

    public function getEntity()
    {
        return $this->entityFactory->getPriceEntity();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Prices';
    }

    /**
     * @param Price $entity
     */
    public function preSave($entity): void
    {
        if (!$entity->hasExternalReference()) {
            $this->priceRegister->registerPrice($entity);
        }

        if (!$entity->getId()) {
            $entity->setCreatedAt(new \DateTime('now'));
        }
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('amount', 'text')
            ->addField('currency', 'text')
            ->addField('schedule', 'text');

        return $listView;
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $products = $this->productRepository->getAll();

        $entityForm->section('Main')
                ->field('amount')
                ->field('currency', 'choice', ['choices' => ['Euro' => 'EUR', 'British Pounds' => 'GBP', 'US Dollars' => 'USD', 'AU Dollars' => 'AUD']])
                ->field('recurring', 'checkbox', ['required' => false])
                ->field('schedule', 'choice', ['choices' => [null => null, 'Yearly' => 'year', 'Monthly' => 'month', 'Weekly' => 'week']])
            ->field('includingTax', 'checkbox', ['required' => false])
            ->field('public', 'checkbox', ['required' => false])
                ->field('product', 'choice', ['choices' => $products, 'choice_label' => 'name', 'choice_value' => 'id'], false)
            ->end();

        return $entityForm;
    }
}
