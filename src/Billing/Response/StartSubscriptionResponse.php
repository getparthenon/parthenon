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

namespace Parthenon\Billing\Response;

use Parthenon\Billing\Entity\Subscription;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class StartSubscriptionResponse
{
    public const CODE_REQUEST_INVALID = '320000';
    public const CODE_NO_BILLING_DETAILS = '320001';
    public const CODE_UNSUPPORTED_PAYMENT_PROVIDER = '320002';
    public const CODE_PLAN_NOT_FOUND = '320003';
    public const CODE_PLAN_PRICE_NOT_FOUND = '320004';
    public const CODE_GENERAL_ERROR = '320005';

    public static function createGeneralError(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_GENERAL_ERROR,
        ];
    }

    public static function createInvalidRequestResponse(ConstraintViolationListInterface $errors): array
    {
        return [
            'success' => false,
            'code' => static::CODE_REQUEST_INVALID,
        ];
    }

    public static function createNoBillingDetails(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_NO_BILLING_DETAILS,
        ];
    }

    public static function createUnsupportedPaymentProvider(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_UNSUPPORTED_PAYMENT_PROVIDER,
        ];
    }

    public static function createPlanNotFound(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_PLAN_NOT_FOUND,
        ];
    }

    public static function createPlanPriceNotFound(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_PLAN_PRICE_NOT_FOUND,
        ];
    }

    public static function createSuccessResponse(Subscription $subscription): array
    {
        return [
            'success' => true,
        ];
    }
}
