<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Billing\Repository\Orm;

use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Repository\SubscriptionPlanRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;

class SubscriptionPlanRepository extends DoctrineCrudRepository implements SubscriptionPlanRepositoryInterface
{
    public function getAll(): array
    {
        return $this->entityRepository->findAll();
    }

    public function getAllForProduct(Product $product): array
    {
        return $this->entityRepository->findBy(['product' => $product]);
    }

    public function getByCodeName(string $codeName): SubscriptionPlan
    {
        $subscriptionPlan = $this->entityRepository->findOneBy(['codeName' => $codeName]);

        if (!$subscriptionPlan instanceof SubscriptionPlan) {
            throw new NoEntityFoundException(sprintf("No subscription plan found for '%s'", $codeName));
        }

        return $subscriptionPlan;
    }
}
