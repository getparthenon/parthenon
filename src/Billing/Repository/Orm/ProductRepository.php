<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Repository\Orm;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\ProductRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class ProductRepository extends DoctrineCrudRepository implements ProductRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getByExternalReference(string $externalReference): Product
    {
        $product = $this->entityRepository->findOneBy(['externalReference' => $externalReference]);

        if (!$product instanceof Product) {
            throw new NoEntityFoundException(sprintf("Unable to find product for external reference '%s'", $externalReference));
        }

        return $product;
    }
}
