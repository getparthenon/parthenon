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
