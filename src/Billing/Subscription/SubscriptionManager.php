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

namespace Parthenon\Billing\Subscription;

use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Obol\SubscriptionFactoryInterface;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;

class SubscriptionManager implements SubscriptionManagerInterface
{
    public function __construct(
        private PaymentDetailsRepositoryInterface $paymentDetailsRepository,
        private ProviderInterface $provider,
        private BillingDetailsFactoryInterface $billingDetailsFactory,
        private PaymentFactoryInterface $paymentFactory,
        private SubscriptionFactoryInterface $subscriptionFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private PlanManagerInterface $planManager,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    ) {
    }

    public function startSubscription(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        if (!$startSubscriptionDto->hasPaymentDetailsId()) {
            $paymentDetails = $this->paymentDetailsRepository->getDefaultPaymentDetailsForCustomer($customer);
        } else {
            $paymentDetails = $this->paymentDetailsRepository->findById($startSubscriptionDto->getPaymentDetailsId());
        }
        $billingDetails = $this->billingDetailsFactory->createFromCustomerAndPaymentDetails($customer, $paymentDetails);

        $plan = $this->planManager->getPlanByName($startSubscriptionDto->getPlanName());
        $planPrice = $plan->getPriceForPaymentSchedule($startSubscriptionDto->getSchedule(), $startSubscriptionDto->getCurrency());

        $obolSubscription = $this->subscriptionFactory->createSubscription($billingDetails, $planPrice, $startSubscriptionDto->getSeatNumbers());

        $subscriptionCreationResponse = $this->provider->payments()->startSubscription($obolSubscription);
        if ($subscriptionCreationResponse->hasCustomerCreation()) {
            $customer->setPaymentProviderDetailsUrl($subscriptionCreationResponse->getCustomerCreation()->getDetailsUrl());
            $customer->setExternalCustomerReference($subscriptionCreationResponse->getCustomerCreation()->getReference());
        }
        $payment = $this->paymentFactory->fromSubscriptionCreation($subscriptionCreationResponse);
        $this->paymentRepository->save($payment);

        $subscription = new Subscription();
        $subscription->setPlanName($plan->getName());
        $subscription->setPaymentSchedule($startSubscriptionDto->getSchedule());
        $subscription->setActive(true);
        $subscription->setMoneyAmount($planPrice->getPriceAsMoney());
        $subscription->setStatus(\Parthenon\Billing\Entity\EmbeddedSubscription::STATUS_ACTIVE);
        $subscription->setExternalReference($subscriptionCreationResponse->getSubscriptionId());
        $subscription->setMainSubscription(true);
        $subscription->setSeats($startSubscriptionDto->getSeatNumbers());
        $subscription->setCreatedAt(new \DateTime());
        $subscription->setUpdatedAt(new \DateTime());
        $subscription->setCustomer($customer);

        if ($plan->hasEntityId()) {
            $subscriptionPlan = $this->subscriptionPlanRepository->findById($plan->getEntityId());
            $subscription->setSubscriptionPlan($subscriptionPlan);
        }

        if ($planPrice->hasEntityId()) {
            $price = $this->priceRepository->findById($planPrice->getEntityId());
            $subscription->setPrice($price);
        }

        $this->subscriptionRepository->save($subscription);

        return $subscription;
    }
}
