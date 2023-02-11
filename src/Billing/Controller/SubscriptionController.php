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

namespace Parthenon\Billing\Controller;

use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubscriptionController
{
    public function startSubscriptionWithPaymentDetails(
        CustomerProviderInterface $customerProvider,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,

        ProviderInterface $provider,
    ) {
        $customer = $customerProvider->getCurrentCustomer();

        try {
            $paymentDetails = $paymentDetailsRepository->getDefaultPaymentDetailsForCustomer($customer);
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true], JsonResponse::HTTP_BAD_REQUEST);
    }
}
