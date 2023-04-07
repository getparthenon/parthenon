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
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class PaymentRepository extends DoctrineCrudRepository implements PaymentRepositoryInterface
{
    public function getPaymentsForSubscription(Subscription $subscription): array
    {
        $qb = $this->entityRepository->createQueryBuilder('p');
        $qb->where(':subscription MEMBER OF p.subscriptions')
            ->setParameter('subscription', $subscription)
            ->orderBy('p.createdAt', 'DESC');

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getLastPaymentForSubscription(Subscription $subscription): Payment
    {
        $qb = $this->entityRepository->createQueryBuilder('p');
        $qb->where(':subscription MEMBER OF p.subscriptions')
            ->setParameter('subscription', $subscription)
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }

    public function getLastPaymentForCustomer(CustomerInterface $customer): Payment
    {
        return $this->entityRepository->findOneBy(['customer' => $customer], ['createdAt' => 'DESC']);
    }
}
