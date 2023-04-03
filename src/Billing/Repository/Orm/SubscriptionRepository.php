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
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Repository\SubscriptionRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionRepository extends DoctrineCrudRepository implements SubscriptionRepositoryInterface
{
    public function hasActiveMainSubscription(CustomerInterface $customer): bool
    {
        try {
            $this->getActiveMainSubscription($customer);
        } catch (NoEntityFoundException $exception) {
            return false;
        }

        return true;
    }

    public function getActiveMainSubscription(CustomerInterface $customer): Subscription
    {
        $subscription = $this->entityRepository->findOneBy(['customer' => $customer, 'active' => true, 'mainSubscription' => true]);

        if (!$subscription instanceof Subscription) {
            throw new NoEntityFoundException();
        }

        return $subscription;
    }

    public function getAllForCustomer(CustomerInterface $customer): array
    {
        return $this->entityRepository->findBy(['customer' => $customer]);
    }
}
