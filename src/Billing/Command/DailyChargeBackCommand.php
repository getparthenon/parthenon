<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting daily charge back command');

        $chargeBacks = $this->provider->chargeBacks()->createdSinceYesterday();

        foreach ($chargeBacks as $obolChargeBack) {
            $chargeBack = $this->syncer->sync($obolChargeBack);
        }
    }
}
