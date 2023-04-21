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

enum ChargeBackStatus: string
{
    case WARNING_UNDER_REVIEW = 'warning_under_review';
    case WARNING_NEEDS_RESPONSE = 'warning_need_response';
    case NEED_RESPONSE = 'needs_response';
    case UNDER_REVIEW = 'under_review';
    case CHARGE_REFUNDED = 'charge_refunded';
    case WON = 'won';
    case LOST = 'lost';
}
