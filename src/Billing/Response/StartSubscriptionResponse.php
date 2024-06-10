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

namespace Parthenon\Billing\Response;

use Obol\Model\Enum\ChargeFailureReasons;
use Parthenon\Billing\Entity\Subscription;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class StartSubscriptionResponse
{
    public const CODE_REQUEST_INVALID = '320000';
    public const CODE_NO_BILLING_DETAILS = '320001';
    public const CODE_UNSUPPORTED_PAYMENT_PROVIDER = '320002';
    public const CODE_PLAN_NOT_FOUND = '320003';
    public const CODE_PLAN_PRICE_NOT_FOUND = '320004';
    public const CODE_GENERAL_ERROR = '320005';
    public const CODE_PAYMENT_FAILURE_ERROR = '320006';
    public const CODE_NO_PAYMENT_DETAILS_ERROR = '320007';

    public static function createGeneralError(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_GENERAL_ERROR,
        ];
    }

    public static function createInvalidRequestResponse(ConstraintViolationListInterface $errors): array
    {
        $errorOutput = [];

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $errorOutput[$error->getPropertyPath()] = $error->getMessage();
        }

        return [
            'success' => false,
            'code' => static::CODE_REQUEST_INVALID,
            'errors' => $errorOutput,
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

    public static function createPaymentFailed(ChargeFailureReasons $chargeFailureReason): array
    {
        return [
            'success' => false,
            'code' => static::CODE_PAYMENT_FAILURE_ERROR,
            'reason' => $chargeFailureReason->value,
        ];
    }

    public static function createNoPaymentDetails(): array
    {
        return [
            'success' => false,
            'code' => static::CODE_NO_BILLING_DETAILS,
        ];
    }

    public static function createSuccessResponse(Subscription $subscription): array
    {
        return [
            'success' => true,
            'subscription' => [
                'id' => $subscription->getId(),
            ],
        ];
    }
}
