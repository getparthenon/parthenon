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
use Parthenon\Billing\Enum\ChargeBackReason;
use Parthenon\Billing\Enum\ChargeBackStatus;
use Parthenon\Billing\Factory\EntityFactory;
use Parthenon\Billing\Repository\ChargeBackRepositoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class ChargeBackSyncer implements ChargeBackSyncerInterface
{
    public function __construct(
        private ChargeBackRepositoryInterface $chargeBackRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private EntityFactory $entityFactory,
    ) {
    }

    public function sync(AbstractDispute|\Obol\Model\ChargeBack\ChargeBack $event): ChargeBack
    {
        try {
            $chargeBack = $this->chargeBackRepository->getByExternalReference($event->getId());
        } catch (NoEntityFoundException $e) {
            $chargeBack = $this->entityFactory->getChargeBackEntity();
            $payment = $this->paymentRepository->getPaymentForReference($event->getPaymentReference());
            $chargeBack->setExternalReference($event->getId());
            $chargeBack->setPayment($payment);
            $chargeBack->setCustomer($payment->getCustomer());
            $chargeBack->setCreatedAt(new \DateTime('now'));
        }

        $chargeBack->setStatus(ChargeBackStatus::fromName($event->getStatus()));
        $chargeBack->setReason(ChargeBackReason::fromName($event->getReason()));
        $chargeBack->setUpdatedAt(new \DateTime('now'));

        return $chargeBack;
    }
}
