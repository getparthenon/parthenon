<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
}
