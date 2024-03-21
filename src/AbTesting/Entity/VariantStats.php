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

namespace Parthenon\AbTesting\Entity;

class VariantStats
{
    private int $numberOfSessions = 0;

    private int $numberOfConversions = 0;

    private float $conversionPercentage = 0.0;

    public function getNumberOfSessions(): int
    {
        return $this->numberOfSessions;
    }

    public function setNumberOfSessions(int $numberOfSessions): void
    {
        $this->numberOfSessions = $numberOfSessions;
    }

    public function getNumberOfConversions(): int
    {
        return $this->numberOfConversions;
    }

    public function setNumberOfConversions(int $numberOfConversions): void
    {
        $this->numberOfConversions = $numberOfConversions;
    }

    public function getConversionPercentage(): float
    {
        return $this->conversionPercentage;
    }

    public function setConversionPercentage(float $conversionPercentage): void
    {
        $this->conversionPercentage = $conversionPercentage;
    }
}
