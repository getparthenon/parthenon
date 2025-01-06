<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChargeSucceededHandler implements HandlerInterface
{
    use LoggerAwareTrait;

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
            $this->getLogger()->info('Found payment in database', ['payment_reference' => $event->getPaymentReference()]);
        } catch (NoEntityFoundException $exception) {
            $payment = $this->paymentFactory->fromChargeEvent($event);
            $this->getLogger()->info('Creating payment', ['payment_reference' => $event->getPaymentReference()]);
        }
        $payment->setStatus(PaymentStatus::COMPLETED);
        $payment->setUpdatedAt(new \DateTime('now'));

        if ($event->hasExternalCustomerId()) {
            try {
                $customer = $this->customerManager->getCustomerForReference($event->getExternalCustomerId());
                $payment->setCustomer($customer);
                $this->getLogger()->info('Found customer');
            } catch (NoCustomerException $e) {
                // Handle error some how.
                $this->getLogger()->warning('No customer found', ['external_customer_id' => $event->getExternalCustomerId()]);
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
