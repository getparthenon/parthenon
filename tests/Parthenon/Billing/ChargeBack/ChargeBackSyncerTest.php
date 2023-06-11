<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
