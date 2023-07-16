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

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\ListView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\Settings;
use Parthenon\Billing\Factory\EntityFactoryInterface;
use Parthenon\Billing\Repository\PaymentRepositoryInterface;

class PaymentSection extends AbstractSection
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private EntityFactoryInterface $entityFactory,
    ) {
    }

    public function getUrlTag(): string
    {
        return 'billing-payments';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->paymentRepository;
    }

    public function getEntity()
    {
        return $this->entityFactory->getPaymentEntity();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Payments';
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView
            ->addField('customer', 'entity')
            ->addField('provider', 'text')
            ->addField('moneyAmount', 'text')
            ->addField('completed', 'boolean');

        return $listView;
    }

    public function getSettings(): Settings
    {
        return new Settings(['edit' => false, 'create' => false]);
    }
}
