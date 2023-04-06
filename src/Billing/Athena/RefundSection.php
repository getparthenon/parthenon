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

namespace Parthenon\Billing\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\Settings;
use Parthenon\Billing\Entity\Refund;
use Parthenon\Billing\Repository\RefundRepositoryInterface;

class RefundSection extends AbstractSection
{
    public function __construct(private RefundRepositoryInterface $refundRepository)
    {
    }

    public function getUrlTag(): string
    {
        return 'refund';
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->refundRepository;
    }

    public function getEntity()
    {
        return new Refund();
    }

    public function getMenuSection(): string
    {
        return 'Billing';
    }

    public function getMenuName(): string
    {
        return 'Refunds';
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('customer', 'text')
            ->addField('amount', 'text')
            ->addField('currency', 'text')
            ->addField('createdAt', 'text');

        return $listView;
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView->section('Main')
            ->field('customer')
            ->field('amount')
            ->field('currency')
            ->field('createdAt')
            ->end();

        return $readView;
    }

    public function getSettings(): Settings
    {
        return new Settings(['create' => false, 'edit' => false]);
    }
}
