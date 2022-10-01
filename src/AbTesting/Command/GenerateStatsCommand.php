<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Command;

use Parthenon\AbTesting\Calculation\Calculate;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateStatsCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'parthenon:ab-testing:generate-stats';

    private ExperimentRepositoryInterface $experimentRepository;
    private Calculate $calculate;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, Calculate $calculate)
    {
        parent::__construct(null);
        $this->experimentRepository = $experimentRepository;
        $this->calculate = $calculate;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating stats');
        $this->getLogger()->info('Generate stats');

        foreach ($this->experimentRepository->findAll() as $experiment) {
            $this->calculate->process($experiment);
        }

        return 0;
    }
}
