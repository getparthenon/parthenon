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

namespace Parthenon\Billing\Factory;

use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\PriceInterface;
use Parthenon\Billing\Entity\ProductInterface;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\ReceiptLineInterface;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlanInterface;

interface EntityFactoryInterface
{
    public function getProductEntity(): ProductInterface;

    public function getPriceEntity(): PriceInterface;

    public function getSubscriptionPlanEntity(): SubscriptionPlanInterface;

    public function getSubscriptionEntity(): Subscription;

    public function getPaymentEntity(): Payment;

    public function getChargeBackEntity(): ChargeBack;

    public function getReceipt(): ReceiptInterface;

    public function getReceiptLine(): ReceiptLineInterface;
}
