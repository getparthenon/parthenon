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

namespace Parthenon\Billing\BillaBear;

use BillaBear\ApiException;
use BillaBear\Model\SubscriptionStartBody;
use Parthenon\Billing\Dto\StartSubscriptionDto;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Enum\BillingChangeTiming;
use Parthenon\Billing\Event\SubscriptionCreated;
use Parthenon\Billing\Exception\NoPaymentDetailsException;
use Parthenon\Billing\Exception\PaymentFailureException;
use Parthenon\Billing\Plan\Plan;
use Parthenon\Billing\Plan\PlanManagerInterface;
use Parthenon\Billing\Plan\PlanPrice;
use Parthenon\Billing\Subscription\SubscriptionManagerInterface;
use Parthenon\Common\Exception\GeneralException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SubscriptionManager implements SubscriptionManagerInterface
{
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
            $response = $this->sdkFactory->createSubscriptionsApi()->customerStartSubscription($subscriptionStart, $customerId);
        } catch (ApiException $apiException) {
            if ($apiException->getResponseObject() instanceof ResponseInterface) {
                match ($apiException->getResponseObject()->getStatusCode()) {
                    402 => new PaymentFailureException('Payment failed', previous: $apiException),
                    406 => new NoPaymentDetailsException('No payment details', previous: $apiException),
                    default => new GeneralException(previous: $apiException),
                };
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
        // TODO: Implement cancelSubscriptionAtEndOfCurrentPeriod() method.
    }

    public function cancelSubscriptionInstantly(Subscription $subscription): Subscription
    {
        // TODO: Implement cancelSubscriptionInstantly() method.
    }

    public function cancelSubscriptionOnDate(Subscription $subscription, \DateTimeInterface $dateTime): Subscription
    {
        // TODO: Implement cancelSubscriptionOnDate() method.
    }

    public function changeSubscriptionPrice(Subscription $subscription, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        // TODO: Implement changeSubscriptionPrice() method.
    }

    public function changeSubscriptionPlan(Subscription $subscription, SubscriptionPlan $plan, Price $price, BillingChangeTiming $billingChangeTiming): void
    {
        // TODO: Implement changeSubscriptionPlan() method.
    }
}
