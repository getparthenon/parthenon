<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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
        return $this->entityRepository->findBy(['product' => $product, 'isDeleted' => false]);
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
