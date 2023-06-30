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

namespace Parthenon\Billing\Webhook\Handler;

use Obol\Model\Events\ChargeSucceeded;
use Obol\Model\Events\EventInterface;
use Parthenon\Billing\Customer\CustomerManagerInterface;
use Parthenon\Billing\Enum\PaymentStatus;
use Parthenon\Billing\Event\PaymentCreated;
use Parthenon\Billing\Exception\NoCustomerException;
use Parthenon\Billing\Obol\PaymentFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Billing\Subscription\PaymentLinkerInterface;
use Parthenon\Billing\Subscription\SchedulerInterface;
use Parthenon\Billing\Webhook\HandlerInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChargeSucceededHandler implements HandlerInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CustomerManagerInterface $customerManager,
        private PaymentFactoryInterface $paymentFactory,
        private PaymentLinkerInterface $eventLinker,
        private EventDispatcherInterface $dispatcher,
        private SchedulerInterface $scheduler,
    ) {
    }

    public function supports(EventInterface $event): bool
    {
        return $event instanceof ChargeSucceeded;
    }

    /**
     * @param ChargeSucceeded $event
     */
    public function handle(EventInterface $event): void
    {
        try {
            $payment = $this->paymentRepository->getPaymentForReference($event->getPaymentReference());
        } catch (NoEntityFoundException $exception) {
            $payment = $this->paymentFactory->fromChargeEvent($event);
        }
        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setUpdatedAt(new \DateTime('now'));

        if ($event->hasExternalCustomerId()) {
            try {
                $customer = $this->customerManager->getCustomerForReference($event->getExternalCustomerId());
                $payment->setCustomer($customer);
            } catch (NoCustomerException $e) {
                // Handle error some how.
            }
        }

        $this->eventLinker->linkToSubscription($payment, $event);

        $this->paymentRepository->save($payment);

        foreach ($payment->getSubscriptions() as $subscription) {
            $this->scheduler->scheduleNextCharge($subscription);
        }
        $this->dispatcher->dispatch(new PaymentCreated($payment), PaymentCreated::NAME);
    }
}
