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
