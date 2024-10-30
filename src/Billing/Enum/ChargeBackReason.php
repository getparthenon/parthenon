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

namespace Parthenon\Billing\Enum;

enum ChargeBackReason: string
{
    case BANK_CANNOT_PROCESS = 'bank_cannot_process';
    case CHECK_RETURNED = 'check_returned';
    case CREDIT_NOT_PROCESSED = 'credit_not_processed';
    case CUSTOMER_INITIATED = 'customer_initiated';
    case DEBIT_NOT_AUTHORISED = 'debit_not_authorised';
    case DUPLICATE = 'duplicate';
    case FRAUDULENT = 'fraudulent';
    case GENERAL = 'general';
    case INCORRECT_ACCOUNT_DETAILS = 'incorrect_account_details';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case PRODUCT_NOT_RECEIVED = 'product_not_received';
    case PRODUCT_UNACCEPTABLE = 'product_unacceptable';
    case SUBSCRIPTION_CANCELED = 'subscription_canceled';
    case UNRECOGNIZED = 'unrecognized';

    public static function fromName(string $name): self
    {
        foreach (self::cases() as $status) {
            if ($name === $status->value) {
                return $status;
            }
        }
        throw new \ValueError("$name is not a valid backing value for enum ".self::class);
    }
}
