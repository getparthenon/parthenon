<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Billing\Repository\Orm;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

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

    public function getPaymentsForCustomer(CustomerInterface $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }

    public function getPaymentForReference(string $reference): Payment
    {
        $payment = $this->entityRepository->findOneBy(['paymentReference' => $reference]);

        if (!$payment instanceof Payment) {
            throw new NoEntityFoundException(sprintf("No payment for '%s'", $reference));
        }

        return $payment;
    }

    public function getPaymentsForCustomerDuring(\DateTimeInterface $startDate, \DateTimeInterface $endDate, CustomerInterface $customer): array
    {
        $qb = $this->entityRepository->createQueryBuilder('p');
        $qb->where('createdAt >=  :startDate')
            ->andWhere('createdAt <= :endDate')
            ->andWhere('customer = :customer')
            ->setParameter('startDate', $startDate)
            ->setParameter('endData', $endDate)
            ->setParameter('customer', $customer);

        $query = $qb->getQuery();
    }
}
