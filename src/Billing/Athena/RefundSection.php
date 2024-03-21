<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
