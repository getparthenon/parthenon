<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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
