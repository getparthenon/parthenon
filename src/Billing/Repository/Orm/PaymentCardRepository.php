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

use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\PaymentCard;
use Parthenon\Billing\Repository\PaymentCardRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\DoctrineRepository;

class PaymentCardRepository extends DoctrineRepository implements PaymentCardRepositoryInterface
{
    public function getPaymentCardForCustomer(CustomerInterface $customer): array
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->where('pd.deleted = false')
            ->andWhere('pd.customer = :customer')
            ->setParameter('customer', $customer);
        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    public function markAllCustomerCardsAsNotDefault(CustomerInterface $customer): void
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->update(PaymentCard::class, 'pd')
            ->set('pd.defaultPaymentOption', 'false')
            ->where('pd.customer = :customer')
            ->setParameter(':customer', $customer);
        $qb->getQuery()->execute();
    }

    public function getDefaultPaymentCardForCustomer(CustomerInterface $customer): PaymentCard
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->where('pd.deleted = false')
            ->andWhere('pd.defaultPaymentOption = true')
            ->andWhere('pd.customer = :customer')
            ->setParameter('customer', $customer);
        $query = $qb->getQuery();
        $query->execute();

        $paymentDetails = $query->getOneOrNullResult();

        if (!$paymentDetails instanceof PaymentCard) {
            throw new NoEntityFoundException();
        }

        return $paymentDetails;
    }

    public function getPaymentCardForCustomerAndReference(CustomerInterface $customer, string $reference): PaymentCard
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->where('pd.deleted = false')
            ->andWhere('pd.customer = :customer')
            ->andWhere('pd.storedPaymentReference = :reference')
            ->setParameter('customer', $customer)
            ->setParameter('reference', $reference);
        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult()[0] ?? throw new NoEntityFoundException();
    }

    public function getPaymentCardForReference(string $reference): PaymentCard
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->where('pd.deleted = false')
            ->andWhere('pd.storedPaymentReference = :reference')
            ->setParameter('reference', $reference);
        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult()[0] ?? throw new NoEntityFoundException();
    }
}
