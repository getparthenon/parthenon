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

namespace Parthenon\Billing\ChargeBack;

use App\Parthenon\Billing\Event\ChargeBackUpdated;
use Obol\Model\Events\AbstractDispute;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Enum\ChargeBackReason;
use Parthenon\Billing\Enum\ChargeBackStatus;
use Parthenon\Billing\Event\ChargeBackCreated;
use Parthenon\Billing\Factory\EntityFactory;
use Parthenon\Billing\Repository\ChargeBackRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ChargeBackSyncer implements ChargeBackSyncerInterface
{
    public function __construct(
        private ChargeBackRepositoryInterface $chargeBackRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private EntityFactory $entityFactory,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function sync(AbstractDispute|\Obol\Model\ChargeBack\ChargeBack $event): ChargeBack
    {
        try {
            $chargeBack = $this->chargeBackRepository->getByExternalReference($event->getId());
            $symfonyEvent = new ChargeBackUpdated($chargeBack);
        } catch (NoEntityFoundException $e) {
            $chargeBack = $this->entityFactory->getChargeBackEntity();
            $payment = $this->paymentRepository->getPaymentForReference($event->getPaymentReference());
            $chargeBack->setExternalReference($event->getId());
            $chargeBack->setPayment($payment);
            if ($payment->hasCustomer()) {
                $chargeBack->setCustomer($payment->getCustomer());
            }
            $chargeBack->setCreatedAt(new \DateTime('now'));
            $symfonyEvent = new ChargeBackCreated($chargeBack);
        }

        $chargeBack->setStatus(ChargeBackStatus::fromName($event->getStatus()));
        $chargeBack->setReason(ChargeBackReason::fromName($event->getReason()));
        $chargeBack->setUpdatedAt(new \DateTime('now'));

        $this->chargeBackRepository->save($chargeBack);
        $this->dispatcher->dispatch($symfonyEvent, $symfonyEvent::NAME);

        return $chargeBack;
    }
}
