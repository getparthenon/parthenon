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

namespace Parthenon\Billing\Factory;

use Parthenon\Billing\Entity\ChargeBack;
use Parthenon\Billing\Entity\Payment;
use Parthenon\Billing\Entity\Price;
use Parthenon\Billing\Entity\PriceInterface;
use Parthenon\Billing\Entity\Product;
use Parthenon\Billing\Entity\ProductInterface;
use Parthenon\Billing\Entity\Receipt;
use Parthenon\Billing\Entity\ReceiptInterface;
use Parthenon\Billing\Entity\ReceiptLine;
use Parthenon\Billing\Entity\ReceiptLineInterface;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Entity\Subscription;
use Parthenon\Billing\Entity\SubscriptionPlan;
use Parthenon\Billing\Entity\SubscriptionPlanInterface;

class EntityFactory implements EntityFactoryInterface
{
    public function getSubscriptionEntity(): Subscription
    {
        return new Subscription();
    }

    public function getPaymentEntity(): Payment
    {
        return new Payment();
    }

    public function getChargeBackEntity(): ChargeBack
    {
        return new ChargeBack();
    }

    public function getProductEntity(): ProductInterface
    {
        return new Product();
    }

    public function getPriceEntity(): PriceInterface
    {
        return new Price();
    }

    public function getSubscriptionPlanEntity(): SubscriptionPlanInterface
    {
        return new SubscriptionPlan();
    }

    public function getReceipt(): ReceiptInterface
    {
        return new Receipt();
    }

    public function getReceiptLine(): ReceiptLineInterface
    {
        return new ReceiptLine();
    }

    public function getRefundEntity(): Refund
    {
        return new Refund();
    }
}
