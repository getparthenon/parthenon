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

namespace Parthenon\Billing\PaymentDetails;

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DefaultPaymentManagerTest extends TestCase
{
    public function testRepositoryIsCalled()
    {
        $id = 'id-here';
        $paymentDetails = $this->createMock(PaymentDetails::class);
        $paymentDetails->method('getId')->willReturn($id);
        $customer = $this->createMock(CustomerInterface::class);
        $repository = $this->createMock(PaymentDetailsRepositoryInterface::class);

        $paymentDetails->expects($this->once())->method('setDefaultPaymentOption')->with(true);

        $repository->expects($this->once())->method('markAllCustomerDetailsAsNotDefault')->with($customer);
        $repository->expects($this->once())->method('save')->with($paymentDetails);
        $repository->method('findById')->with($id)->willReturn($paymentDetails);

        $subject = new DefaultPaymentManager($repository);
        $subject->makePaymentDetailsDefault($customer, $paymentDetails);
    }
}
