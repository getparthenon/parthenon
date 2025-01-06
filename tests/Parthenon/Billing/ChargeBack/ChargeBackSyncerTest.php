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

use Obol\Model\Events\DisputeCreation;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Factory\EntityFactory;
use Parthenon\Billing\Repository\ChargeBackRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ChargeBackSyncerTest extends TestCase
{
    public function testCreateChargeBackCreated()
    {
        $cbReference = 'cb_dfjkdslkf';
        $paymentReference = 'ch_dskjfdkj';
        $event = new DisputeCreation();
        $event->setId($cbReference);
        $event->setPaymentReference($paymentReference);
        $event->setStatus('warning_under_review');
        $event->setReason('product_unacceptable');

        $cbRepository = $this->createMock(ChargeBackRepositoryInterface::class);
        $paymentRepository = $this->createMock(PaymentRepositoryInterface::class);
        $entityFactory = $this->createMock(EntityFactory::class);
        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $subject = new ChargeBackSyncer($cbRepository, $paymentRepository, $entityFactory, $dispatcher);

        $cbRepository->method('getByExternalReference')->willThrowException(new NoEntityFoundException());
        $entityFactory->method('getChargeBackEntity')->willReturn(new ChargeBack());

        $paymentRepository->method('getPaymentForReference')->with($this->equalTo($paymentReference))->willReturn(new Payment());

        $cbRepository->expects($this->once())->method('save');
        $dispatcher->expects($this->once())->method('dispatch');

        $subject->sync($event);
    }
}
