<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Repository\Orm;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\CustomerInterface;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Enum\SubscriptionStatus;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionRepository extends DoctrineCrudRepository implements SubscriptionRepositoryInterface
{
    public function hasActiveSubscription(CustomerInterface $customer): bool
    {
        try {
            $this->getOneActiveSubscriptionForCustomer($customer);
        } catch (NoEntityFoundException $exception) {
            return false;
        }

        return true;
    }

    public function getOneActiveSubscriptionForCustomer(CustomerInterface $customer): Subscription
    {
        $subscription = $this->entityRepository->findOneBy(['customer' => $customer, 'active' => true]);

        if (!$subscription instanceof Subscription) {
            throw new NoEntityFoundException();
        }

        return $subscription;
    }

    public function getAllForCustomer(CustomerInterface $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }

    public function getAllActiveForCustomer(CustomerInterface $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer, 'active' => true]);
    }

    public function updateValidUntilForAllActiveSubscriptions(CustomerInterface $customer, string $mainExternalReference, \DateTimeInterface $validUntil): void
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->update()
            ->set('s.validUntil', ':validUntil')
            ->set('s.updatedAt', ':now')
            ->where('s.customer = :customer')
            ->andWhere('s.status = :active')
            ->andWhere('s.mainExternalReference = :mainExternalReference')
            ->setParameter('customer', $customer)
            ->setParameter('mainExternalReference', $mainExternalReference)
            ->setParameter('validUntil', $validUntil)
            ->setParameter('now', new \DateTime())
            ->setParameter('active', SubscriptionStatus::ACTIVE);
        $query = $qb->getQuery();
        $query->execute();
    }

    public function getActiveSubscriptionCount(CustomerInterface $customer): int
    {
        return $this->entityRepository->count(['customer' => $customer, 'active' => true]);
    }

    public function getForPayment(Payment $payment): array
    {
        $qb = $this->entityRepository->createQueryBuilder('s');
        $qb->where(':payment MEMBER OF p.payment')
            ->setParameter('payment', $payment)
            ->orderBy('p.createdAt', 'DESC');

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getForMainAndChildExternalReference(string $mainExternalReference, string $childExternalReference): Subscription
    {
        $subscription = $this->entityRepository->findOneBy(['mainExternalReference' => $mainExternalReference, 'childExternalReference' => $childExternalReference]);

        if (!$subscription instanceof Subscription) {
            throw new NoEntityFoundException(sprintf("No Subscription found for main external reference '%s' and child reference '%s'", $mainExternalReference, $childExternalReference));
        }

        return $subscription;
    }
}
