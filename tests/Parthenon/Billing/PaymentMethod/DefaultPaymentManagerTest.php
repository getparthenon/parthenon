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

namespace Parthenon\Billing\PaymentMethod;

use Obol\Model\BillingDetails;
use Obol\PaymentServiceInterface;
use Obol\Provider\ProviderInterface;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Obol\BillingDetailsFactoryInterface;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DefaultPaymentManagerTest extends TestCase
{
    public function testRepositoryIsCalled()
    {
        $id = 'id-here';
        $paymentDetails = $this->createMock(PaymentCard::class);
        $paymentDetails->method('getId')->willReturn($id);
        $customer = $this->createMock(CustomerInterface::class);
        $repository = $this->createMock(PaymentCardRepositoryInterface::class);

        $paymentDetails->expects($this->once())->method('setDefaultPaymentOption')->with(true);

        $repository->expects($this->once())->method('markAllCustomerCardsAsNotDefault')->with($customer);
        $repository->expects($this->once())->method('save')->with($paymentDetails);
        $repository->method('findById')->with($id)->willReturn($paymentDetails);

        $billingDetails = new BillingDetails();
        $billingDetailsFactory = $this->createMock(BillingDetailsFactoryInterface::class);
        $billingDetailsFactory->method('createFromCustomerAndPaymentDetails')->willReturn($billingDetails);

        $provider = $this->createMock(ProviderInterface::class);
        $paymentService = $this->createMock(PaymentServiceInterface::class);
        $provider->method('payments')->willReturn($paymentService);
        $paymentService->expects($this->once())->method('makeCardDefault')->with($billingDetails);

        $subject = new DefaultPaymentManager($repository, $provider, $billingDetailsFactory);
        $subject->makePaymentDetailsDefault($customer, $paymentDetails);
    }
}
