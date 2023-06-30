<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Controller;

use Obol\Exception\UnsupportedFunctionalityException;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Exception\NoPlanPriceFoundException;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Billing\Response\StartSubscriptionResponse;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscriptionController
{
    use LoggerAwareTrait;

    #[Route('/billing/subscription/{subscriptionId}/cancel', name: 'parthenon_billing_subscription_cancel', methods: ['POST'])]
    public function cancelSubscription(Request $request, SubscriptionRepositoryInterface $subscriptionRepository, SubscriptionManagerInterface $subscriptionManager): Response
    {
        try {
            $subscription = $subscriptionRepository->getById($request->get('subscriptionId'));
        } catch (NoEntityFoundException $E) {
            return new JsonResponse(status: JsonResponse::HTTP_NOT_FOUND);
        }

        $subscriptionManager->cancelSubscriptionAtEndOfCurrentPeriod($subscription);

        return new JsonResponse(status: JsonResponse::HTTP_ACCEPTED);
    }

    #[Route('/billing/subscription/start', name: 'parthenon_billing_subscription_start_with_payment_details', methods: ['POST'])]
    public function startSubscriptionWithPaymentDetails(
        Request $request,
        CustomerProviderInterface $customerProvider,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        ValidatorInterface $validator,
        SubscriptionManagerInterface $subscriptionManager,
    ): Response {
        $this->getLogger()->info('Starting the subscription');

        try {
            $customer = $customerProvider->getCurrentCustomer();
        } catch (NoCustomerException $exception) {
            $this->getLogger()->error('No customer found when starting subscription with payment details - probable misconfigured firewall.');

            return new JsonResponse(StartSubscriptionResponse::createGeneralError(), JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            /** @var StartSubscriptionDto $subscriptionDto */
            $subscriptionDto = $serializer->deserialize($request->getContent(), StartSubscriptionDto::class, 'json');

            $errors = $validator->validate($subscriptionDto);

            if (count($errors) > 0) {
                return new JsonResponse(StartSubscriptionResponse::createInvalidRequestResponse($errors), JsonResponse::HTTP_BAD_REQUEST);
            }

            $subscription = $subscriptionManager->startSubscriptionWithDto($customer, $subscriptionDto);

            $customerRepository->save($customer);
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(StartSubscriptionResponse::createGeneralError(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (NoPlanPriceFoundException $exception) {
            $this->getLogger()->warning('No price plan found');

            return new JsonResponse(StartSubscriptionResponse::createPlanPriceNotFound(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (NoPlanFoundException $exception) {
            $this->getLogger()->warning('No plan found');

            return new JsonResponse(StartSubscriptionResponse::createPlanNotFound(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (UnsupportedFunctionalityException $exception) {
            $this->getLogger()->error('Payment provider does not support payment details');

            return new JsonResponse(StartSubscriptionResponse::createUnsupportedPaymentProvider(), JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Throwable $t) {
            $this->getLogger()->error('Unknown error while starting a subscription');

            throw $t;
        }

        return new JsonResponse(StartSubscriptionResponse::createSuccessResponse($subscription), JsonResponse::HTTP_CREATED);
    }
}
