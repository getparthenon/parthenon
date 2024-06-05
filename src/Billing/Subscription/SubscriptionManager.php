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

namespace Parthenon\Billing\Subscription;

use Obol\Model\CancelSubscription;
use Obol\Model\Enum\ProrataType;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Event\PaymentCreated;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\Exception\SubscriptionCreationException;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Obol\SubscriptionFactoryInterface;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Repository\PriceRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SubscriptionManager implements SubscriptionManagerInterface
{
    public function __construct(
        private PaymentCardRepositoryInterface $paymentDetailsRepository,
        private ProviderInterface $provider,
        private BillingDetailsFactoryInterface $billingDetailsFactory,
        private PaymentFactoryInterface $paymentFactory,
        private SubscriptionFactoryInterface $subscriptionFactory,
        private PaymentRepositoryInterface $paymentRepository,
        private PlanManagerInterface $planManager,
        private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        private PriceRepositoryInterface $priceRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
        private EntityFactoryInterface $entityFactory,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function startSubscription(
        CustomerInterface $customer,
        SubscriptionPlan|Plan $plan,
        Price|PlanPrice $planPrice,
        ?PaymentCard $paymentDetails = null,
        int $seatNumbers = 1,
        ?bool $hasTrial = null,
        ?int $trialLengthDays = 0,
    ): Subscription {
        $billingDetails = $this->billingDetailsFactory->createFromCustomerAndPaymentDetails($customer, $paymentDetails);
        $obolSubscription = $this->subscriptionFactory->createSubscription($billingDetails, $planPrice, $seatNumbers, $hasTrial ?? $plan->getHasTrial(), $trialLengthDays ?? $plan->getTrialLengthDays());
        $obolSubscription->setStoredPaymentReference($paymentDetails->getStoredPaymentReference());

        if ($this->subscriptionRepository->hasActiveSubscription($customer)) {
            $subscription = $this->subscriptionRepository->getOneActiveSubscriptionForCustomer($customer);

            if ($subscription->getCurrency() != $planPrice->getCurrency()) {
                throw new SubscriptionCreationException("Can't add a child subscription for a different currency");
            }

            $obolSubscription->setParentReference($subscription->getMainExternalReference());
        }

        $subscriptionCreationResponse = $this->provider->payments()->startSubscription($obolSubscription);
        if ($subscriptionCreationResponse->hasCustomerCreation()) {
            $customer->setPaymentProviderDetailsUrl($subscriptionCreationResponse->getCustomerCreation()->getDetailsUrl());
            $customer->setExternalCustomerReference($subscriptionCreationResponse->getCustomerCreation()->getReference());
        }

        $subscription = $this->entityFactory->getSubscriptionEntity();
        $subscription->setPlanName($plan->getName());
        if ($planPrice->isRecurring()) {
            $subscription->setPaymentSchedule($planPrice->getSchedule());
        }
        $subscription->setActive(true);
        $subscription->setMoneyAmount($subscriptionCreationResponse->getPaymentDetails()?->getAmount());
        $subscription->setStatus(SubscriptionStatus::ACTIVE);
        $subscription->setMainExternalReference($subscriptionCreationResponse->getSubscriptionId());
        $subscription->setChildExternalReference($subscriptionCreationResponse->getLineId());
        $subscription->setSeats($seatNumbers);
        $subscription->setCreatedAt(new \DateTime());
        $subscription->setUpdatedAt(new \DateTime());
        $subscription->setStartOfCurrentPeriod(new \DateTime());
        $subscription->setValidUntil($subscriptionCreationResponse->getBilledUntil());
        $subscription->setCustomer($customer);
        $subscription->setMainExternalReferenceDetailsUrl($subscriptionCreationResponse->getDetailsUrl());
        $subscription->setPaymentDetails($paymentDetails);
        $subscription->setTrialLengthDays($obolSubscription->getTrialLengthDays());
        $subscription->setHasTrial($obolSubscription->hasTrial());

        if ($plan instanceof SubscriptionPlan) {
            $subscription->setSubscriptionPlan($plan);
        } elseif ($plan->hasEntityId()) {
            $subscriptionPlan = $this->subscriptionPlanRepository->findById($plan->getEntityId());
            $subscription->setSubscriptionPlan($subscriptionPlan);
        }

        if ($planPrice instanceof Price) {
            $subscription->setPrice($planPrice);
        } elseif ($planPrice->hasEntityId()) {
            $price = $this->priceRepository->findById($planPrice->getEntityId());
            $subscription->setPrice($price);
        }
        $this->subscriptionRepository->save($subscription);
        $this->subscriptionRepository->updateValidUntilForAllActiveSubscriptions($customer, $subscription->getMainExternalReference(), $subscriptionCreationResponse->getBilledUntil());

        $this->dispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);

        $obolPaymentDetails = $subscriptionCreationResponse->getPaymentDetails();
        if ($obolPaymentDetails) {
            $payment = $this->paymentFactory->fromSubscriptionCreation($obolPaymentDetails, $customer);
            $payment->addSubscription($subscription);
            $this->paymentRepository->save($payment);

            $this->dispatcher->dispatch(new PaymentCreated($payment, true), PaymentCreated::NAME);
        }

        return $subscription;
    }

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        if (!$startSubscriptionDto->getPaymentDetailsId()) {
            $paymentDetails = $this->paymentDetailsRepository->getDefaultPaymentCardForCustomer($customer);
        } else {
            $paymentDetails = $this->paymentDetailsRepository->findById($startSubscriptionDto->getPaymentDetailsId());
        }

        $plan = $this->planManager->getPlanByName($startSubscriptionDto->getPlanName());
        $planPrice = $plan->getPriceForPaymentSchedule($startSubscriptionDto->getSchedule(), $startSubscriptionDto->getCurrency());

        return $this->startSubscription($customer, $plan, $planPrice, $paymentDetails, $startSubscriptionDto->getSeatNumbers());
    }

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription
    {
        $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription, false);

        $cancelRequest = new CancelSubscription();
        $cancelRequest->setSubscription($obolSubscription);
        $cancelRequest->setInstantCancel(false);

        $cancellation = $this->provider->payments()->stopSubscription($cancelRequest);

        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->endAtEndOfPeriod();

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);

        $cancelRequest = new CancelSubscription();
        $cancelRequest->setSubscription($obolSubscription);
        $cancelRequest->setInstantCancel(true);

        $cancellation = $this->provider->payments()->stopSubscription($cancelRequest);

        $subscription->setStatus(SubscriptionStatus::CANCELLED);
        $subscription->setActive(false);
        $subscription->endNow();

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);

        $cancelRequest = new CancelSubscription();
        $cancelRequest->setSubscription($obolSubscription);
        $cancelRequest->setInstantCancel(false);

        $cancellation = $this->provider->payments()->stopSubscription($cancelRequest);

        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->setEndedAt($dateTime);
        $subscription->setValidUntil($dateTime);

        $this->subscriptionRepository->save($subscription);
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        $subscription->setPrice($price);
        $subscription->setMoneyAmount($price->getAsMoney());
        $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);
        $obolSubscription->setPriceId($price->getExternalReference());
        $prorataTye = match ($billingChangeTiming) {
            BillingChangeTiming::INSTANTLY => ProrataType::NOW,
            default => ProrataType::NONE,
        };

        $this->provider->subscriptions()->updatePrice($obolSubscription, $prorataTye);
    }

    public function changeSubscriptionPlan(Subscription $subscription, SubscriptionPlan $plan, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        $subscription->setSubscriptionPlan($plan);
        $subscription->setPlanName($plan->getName());
        $subscription->setPrice($price);
        $subscription->setMoneyAmount($price->getAsMoney());

        $obolSubscription = $this->subscriptionFactory->createSubscriptionFromEntity($subscription);
        $obolSubscription->setPriceId($price->getExternalReference());
        $prorataTye = match ($billingChangeTiming) {
            BillingChangeTiming::INSTANTLY => ProrataType::NOW,
            default => ProrataType::NONE,
        };

        $this->provider->subscriptions()->updatePrice($obolSubscription, $prorataTye);
    }
}
