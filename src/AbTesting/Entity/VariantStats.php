<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
