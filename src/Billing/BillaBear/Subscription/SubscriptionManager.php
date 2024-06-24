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

namespace Parthenon\Billing\BillaBear\Subscription;

use BillaBear\ApiException;
use BillaBear\Model\SubscriptionIdCancelBody;
use BillaBear\Model\SubscriptionIdPlanBody;
use BillaBear\Model\SubscriptionIdPriceBody;
use BillaBear\Model\SubscriptionStartBody;
use Obol\Model\Enum\ChargeFailureReasons;
use Parthenon\Billing\BillaBear\SdkFactory;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Event\SubscriptionCancelled;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\Exception\NoPaymentDetailsException;
use Parthenon\Billing\Exception\PaymentFailureException;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\GeneralException;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SubscriptionManager implements SubscriptionManagerInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private SdkFactory $sdkFactory,
        private PlanManagerInterface $planManager,
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
        if (!$plan instanceof Plan) {
            throw new GeneralException('Invalid type of plan given');
        }

        $customerId = $customer->getExternalCustomerReference();
        $payload = [
            'subscription_plan' => $plan->getEntityId(),
            'price' => $planPrice->getEntityId(),
            'seat_numbers' => $seatNumbers,
        ];

        if ($paymentDetails) {
            $payload['payment_details'] = $paymentDetails->getId();
        }

        $subscriptionStart = new SubscriptionStartBody($payload);

        try {
            $response = $this->sdkFactory->createSubscriptionsApi()->createSubscription($subscriptionStart, $customerId);
        } catch (ApiException $apiException) {
            if (402 === $apiException->getCode()) {
                $body = $apiException->getResponseBody();
                $json = json_decode($body, true);
                throw new PaymentFailureException(ChargeFailureReasons::from($json['reason']), previous: $apiException);
            }

            if (406 === $apiException->getCode()) {
                throw new NoPaymentDetailsException('No payment details', previous: $apiException);
            }

            throw new GeneralException(previous: $apiException);
        } catch (\Throwable $exception) {
            new GeneralException($exception->getMessage(), previous: $exception);
        }
        $subscription = new Subscription();
        $subscription->setCustomer($customer);
        $subscription->setId($response->getId());
        $subscription->setValidUntil(new \DateTime($response->getValidUntil()));
        $subscription->setMainExternalReference($response->getMainExternalReference());
        $subscription->setChildExternalReference($response->getChildExternalReference());

        $this->dispatcher->dispatch(new SubscriptionCreated($subscription), SubscriptionCreated::NAME);

        return $subscription;
    }

    public function startSubscriptionWithDto(CustomerInterface $customer, StartSubscriptionDto $startSubscriptionDto): Subscription
    {
        $plan = $this->planManager->getPlanByName($startSubscriptionDto->getPlanName());
        $planPrice = $plan->getPriceForPaymentSchedule($startSubscriptionDto->getSchedule(), $startSubscriptionDto->getCurrency());

        return $this->startSubscription($customer, $plan, $planPrice, null, $startSubscriptionDto->getSeatNumbers());
    }

    public function cancelSubscriptionAtEndOfCurrentPeriod(Subscription $subscription): Subscription
    {
        $cancelBody = new SubscriptionIdCancelBody();
        $cancelBody->setWhen('end-of-run');
        $cancelBody->setRefundType('none');
        $this->sdkFactory->createSubscriptionsApi()->cancelSubscription($cancelBody, $subscription->getId());

        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->endAtEndOfPeriod();
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        $cancelBody = new SubscriptionIdCancelBody();
        $cancelBody->setWhen('instantly');
        $cancelBody->setRefundType('prorate');
        $this->sdkFactory->createSubscriptionsApi()->cancelSubscription($cancelBody, $subscription->getId());

        $subscription->setStatus(SubscriptionStatus::CANCELLED);
        $subscription->setActive(false);
        $subscription->endNow();
        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        $cancelBody = new SubscriptionIdCancelBody();
        $cancelBody->setWhen('specific-date');
        $cancelBody->setRefundType('prorate');
        $cancelBody->setDate($dateTime);
        $this->sdkFactory->createSubscriptionsApi()->cancelSubscription($cancelBody, $subscription->getId());

        $subscription->setStatus(SubscriptionStatus::PENDING_CANCEL);
        $subscription->setEndedAt($dateTime);
        $subscription->setValidUntil($dateTime);

        $this->dispatcher->dispatch(new SubscriptionCancelled($subscription), SubscriptionCancelled::NAME);

        return $subscription;
    }

    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        $payload = new SubscriptionIdPriceBody();
        $payload->setPrice($price->getId());
        $payload->setWhen($billingChangeTiming->value);

        $this->sdkFactory->createSubscriptionsApi()->changeSubscriptionPrice($payload, $subscription->getId());
    }

    public function changeSubscriptionPlan(Subscription $subscription, SubscriptionPlan|Plan $plan, Price|PlanPrice $price, BillingChangeTiming $billingChangeTiming): void
    {
        $payload = new SubscriptionIdPlanBody();
        $payload->setPrice($price->getEntityId());
        $payload->setPlan($plan->getEntityId());
        $payload->setWhen($billingChangeTiming->value);

        $this->sdkFactory->createSubscriptionsApi()->customerChangeSubscriptionPlan($payload, $subscription->getId());
    }
}
