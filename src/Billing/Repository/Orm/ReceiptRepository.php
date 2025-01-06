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
