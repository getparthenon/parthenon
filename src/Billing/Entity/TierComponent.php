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

namespace Parthenon\Billing\Entity;

class TierComponent implements TierComponentInterface
{
    private $id;

    private int $firstUnit;

    private ?int $lastUnit = null;

    private int $unitPrice;

    private int $flatFee;

    private Price $price;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getFirstUnit(): int
    {
        return $this->firstUnit;
    }

    public function setFirstUnit(int $firstUnit): void
    {
        $this->firstUnit = $firstUnit;
    }

    public function getLastUnit(): ?int
    {
        return $this->lastUnit;
    }

    public function setLastUnit(?int $lastUnit): void
    {
        $this->lastUnit = $lastUnit;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getFlatFee(): int
    {
        return $this->flatFee;
    }

    public function setFlatFee(int $flatFee): void
    {
        $this->flatFee = $flatFee;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }
}
