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

namespace Parthenon\Billing\Command;

use Obol\Provider\ProviderInterface;
use Parthenon\Billing\ChargeBack\ChargeBackSyncerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:billing:daily:charge-back', description: 'Sync charge backs for the past day', aliases: ['p:b:d:c'])]
class DailyChargeBackCommand extends Command
{
    public function __construct(
        private ProviderInterface $provider,
        private ChargeBackSyncerInterface $syncer,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting daily charge back command');

        $chargeBacks = $this->provider->chargeBacks()->createdSinceYesterday();

        foreach ($chargeBacks as $obolChargeBack) {
            $chargeBack = $this->syncer->sync($obolChargeBack);
        }
    }
}
