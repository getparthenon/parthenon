<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Obol\ProductRegisterInterface;
use Parthenon\Billing\Repository\ProductRepositoryInterface;

class ProductSection extends AbstractSection
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductRegisterInterface $productRegister,
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
        return new Product();
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
