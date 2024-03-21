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

namespace Parthenon\Billing\Event;

use Parthenon\Billing\Entity\ChargeBack;
use Symfony\Contracts\EventDispatcher\Event;

class ChargeBackCreated extends Event
{
    public const NAME = 'parthenon.billing.charge_back.created';

    public function __construct(private ChargeBack $chargeBack)
    {
    }

    public function getChargeBack(): ChargeBack
    {
        return $this->chargeBack;
    }
}
