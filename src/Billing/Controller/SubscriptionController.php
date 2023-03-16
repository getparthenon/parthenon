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

use Obol\Exception\UnsupportedFunctionalityException;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Exception\NoPlanFoundException;
use Parthenon\Billing\Exception\NoPlanPriceFoundException;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Obol\SubscriptionFactoryInterface;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Response\StartSubscriptionResponse;
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

    #[Route('/billing/subscription/start', name: 'parthenon_billing_subscription_start_with_payment_details', methods: ['POST'])]
    public function startSubscriptionWithPaymentDetails(
        Request $request,
        CustomerProviderInterface $customerProvider,
        PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        BillingDetailsFactoryInterface $billingDetailsFactory,
        PaymentFactoryInterface $paymentFactory,
        PaymentRepositoryInterface $paymentRepository,
        PlanManagerInterface $planManager,
        SerializerInterface $serializer,
        ProviderInterface $provider,
        CustomerRepositoryInterface $customerRepository,
        ValidatorInterface $validator,
        SubscriptionFactoryInterface $subscriptionFactory,
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

            try {
                $paymentDetails = $paymentDetailsRepository->getDefaultPaymentDetailsForCustomer($customer);
            } catch (NoEntityFoundException $e) {
                return new JsonResponse(StartSubscriptionResponse::createNoBillingDetails());
            }
            $billingDetails = $billingDetailsFactory->createFromCustomerAndPaymentDetails($customer, $paymentDetails);

            $plan = $planManager->getPlanByName($subscriptionDto->getPlanName());
            $planPrice = $plan->getPriceForPaymentSchedule($subscriptionDto->getSchedule(), $subscriptionDto->getCurrency());

            $obolSubscription = $subscriptionFactory->createSubscription($billingDetails, $planPrice, $subscriptionDto->getSeatNumbers());

            $subscriptionCreationResponse = $provider->payments()->startSubscription($obolSubscription);
            if ($subscriptionCreationResponse->hasCustomerCreation()) {
                $customer->setPaymentProviderDetailsUrl($subscriptionCreationResponse->getCustomerCreation()->getDetailsUrl());
                $customer->setExternalCustomerReference($subscriptionCreationResponse->getCustomerCreation()->getReference());
            }
            $payment = $paymentFactory->fromSubscriptionCreation($subscriptionCreationResponse);
            $paymentRepository->save($payment);

            $subscription = $customer->getSubscription();
            $subscription->setPlanName($plan->getName());
            $subscription->setPaymentSchedule($subscriptionDto->getSchedule());
            $subscription->setActive(true);
            $subscription->setMoneyAmount($planPrice->getPriceAsMoney());
            $subscription->setStatus(\Parthenon\Billing\Entity\Subscription::STATUS_ACTIVE);

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
        }

        return new JsonResponse(StartSubscriptionResponse::createSuccessResponse($subscription), JsonResponse::HTTP_CREATED);
    }
}
