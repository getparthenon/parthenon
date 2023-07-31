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
}
