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
use Parthenon\Billing\Entity\PaymentDetails;
use Parthenon\Billing\Repository\PaymentDetailsRepositoryInterface;
use Parthenon\Common\Repository\DoctrineRepository;

class PaymentDetailsRepository extends DoctrineRepository implements PaymentDetailsRepositoryInterface
{
    public function getPaymentDetailsForCustomer(CustomerInterface $customer): array
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->where('pd.deleted = false')->andWhere('pd.customer = :customer')->setParameter('customer', $customer);
        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }

    public function markAllCustomerDetailsAsNotDefault(CustomerInterface $customer): void
    {
        $qb = $this->entityRepository->createQueryBuilder('pd');
        $qb->update(PaymentDetails::class, 'pd')
            ->set('pd.defaultPaymentOption', 'false')
            ->where('pd.customer = :customer')
            ->setParameter(':customer', $customer);
        $qb->getQuery()->execute();
    }
}
