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

namespace Parthenon\Billing\Repository\Orm;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Repository\ReceiptRepositoryInterface;

class ReceiptRepository extends DoctrineCrudRepository implements ReceiptRepositoryInterface
{
    public function getForPayment(Payment $payment): array
    {
        $qb = $this->entityRepository->createQueryBuilder('r');
        $qb->where(':payment MEMBER OF r.payments')
            ->setParameter('payment', $payment)
            ->orderBy('r.createdAt', 'DESC');

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
