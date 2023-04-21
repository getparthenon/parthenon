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

namespace Parthenon\Billing\ChargeBack;

use Obol\Model\Events\AbstractDispute;
use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Factory\EntityFactory;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class ChargeBackFactory implements ChargeBackFactoryInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private EntityFactory $entityFactory,
    ) {
    }

    public function buildFromEvent(AbstractDispute $event): ChargeBack
    {
        $chargeBack = $this->entityFactory->getChargeBackEntity();

        $payment = $this->paymentRepository->getPaymentForReference($event->getDisputedPaymentReference());
        $chargeBack->setExternalReference($event->getId());
        $chargeBack->setPayment($payment);
        $chargeBack->setCustomer($payment->getCustomer());
        $chargeBack->setCreatedAt(new \DateTime('now'));
        $chargeBack->setUpdatedAt(new \DateTime('now'));

        return $chargeBack;
    }

    public function buildFromChargeBack(\Obol\Model\ChargeBack\ChargeBack $obolChargeBack): ChargeBack
    {
        $chargeBack = $this->entityFactory->getChargeBackEntity();

        $payment = $this->paymentRepository->getPaymentForReference($obolChargeBack->getPaymentReference());
        $chargeBack->setExternalReference($event->getId());
        $chargeBack->setPayment($payment);
        $chargeBack->setCustomer($payment->getCustomer());
        $chargeBack->setCreatedAt(new \DateTime('now'));
        $chargeBack->setUpdatedAt(new \DateTime('now'));

        return $chargeBack;
    }
}
