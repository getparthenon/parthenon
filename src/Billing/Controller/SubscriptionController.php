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

use Obol\Model\Subscription;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\CustomerRepositoryInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class SubscriptionController
{
    use LoggerAwareTrait;

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
    ) {
        $this->getLogger()->info('Starting the subscription from');

        $customer = $customerProvider->getCurrentCustomer();

        try {
            /** @var StartSubscriptionDto $subscriptionDto */
            $subscriptionDto = $serializer->deserialize($request->getContent(), StartSubscriptionDto::class, 'json');

            $paymentDetails = $paymentDetailsRepository->getDefaultPaymentDetailsForCustomer($customer);
            $billingDetails = $billingDetailsFactory->createFromCustomerAndPaymentDetails($customer, $paymentDetails);

            $plan = $planManager->getPlanByName($subscriptionDto->getPlanName());
            $planPrice = $plan->getPriceForPaymentSchedule($subscriptionDto->getSchedule());

            $obolSubscription = new Subscription();
            $obolSubscription->setBillingDetails($billingDetails);
            $obolSubscription->setSeats($subscriptionDto->getSeatNumbers());
            $obolSubscription->setCostPerSeat($plan->getPrice());
            if ($planPrice->hasPriceId()) {
                $obolSubscription->setPriceId($planPrice->getPriceId());
            }
            $subscriptionCreationResponse = $provider->payments()->startSubscription($obolSubscription);
            $payment = $paymentFactory->fromSubscriptionCreation($subscriptionCreationResponse);
            $paymentRepository->save($payment);

            $subscription = $customer->getSubscription();
            $subscription->setPlanName($plan->getName());
            $subscription->setPaymentSchedule($plan->getPaymentSchedule());
            $subscription->setActive(true);
            $subscription->setMoneyAmount($planPrice->getPriceAsMoney());
            $subscription->setStatus(\Parthenon\Billing\Entity\Subscription::STATUS_ACTIVE);

            $customerRepository->save($customer);
        } catch (NoEntityFoundException $exception) {
            return new JsonResponse(['success' => false], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true], JsonResponse::HTTP_BAD_REQUEST);
    }
}
