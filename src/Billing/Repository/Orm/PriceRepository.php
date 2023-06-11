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
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class PriceRepository extends DoctrineCrudRepository implements PriceRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getAllForProduct(Product $product): array
    {
        return $this->entityRepository->findBy(['product' => $product]);
    }

    public function getByExternalReference(string $externalReference): Price
    {
        $price = $this->entityRepository->findOneBy(['externalReference' => $externalReference]);

        if (!$price instanceof Price) {
            throw new NoEntityFoundException(sprintf("Unable to find price for external reference '%s'", $externalReference));
        }

        return $price;
    }
}
