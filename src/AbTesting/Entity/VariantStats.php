<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
