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

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class ProductSection extends AbstractSection
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductRegisterInterface $productRegister,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function getUrlTag(): string
    {
        return 'billing-product';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->productRepository;
    }

    public function getEntity()
    {
        return $this->entityFactory->getProductEntity();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Product';
    }

    /**
     * @param Product $entity
     */
    public function preSave($entity): void
    {
        if (!$entity->hasExternalReference()) {
            $this->productRegister->registerProduct($entity);
        }
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $entityForm->section('Main')
            ->field('name', 'text')
            ->field('external_reference', 'text', ['required' => false])
            ->end();

        return $entityForm;
    }
}
