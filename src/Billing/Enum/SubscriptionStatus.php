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

namespace Parthenon\Billing\Enum;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';
    case OVERDUE_PAYMENT_OPEN = 'overdue_payment_open';
    case PENDING_CANCEL = 'pending_cancel';
    case OVERDUE_PAYMENT_DISABLED = 'overdue_payment_disabled';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case BLOCKED = 'blocked';
    case TRIAL_ACTIVE = 'trial_active';
    case TRIAL_ENDED = 'trial_ended';
}
