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

namespace Parthenon\AbTesting\Command;

use Parthenon\AbTesting\Calculation\Calculate;
use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:ab-testing:generate-stats')]
final class GenerateStatsCommand extends Command
{
    use LoggerAwareTrait;

    private ExperimentRepositoryInterface $experimentRepository;
    private Calculate $calculate;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, Calculate $calculate)
    {
        parent::__construct(null);
        $this->experimentRepository = $experimentRepository;
        $this->calculate = $calculate;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating stats');
        $this->getLogger()->info('Generate stats');

        foreach ($this->experimentRepository->findAll() as $experiment) {
            $this->calculate->process($experiment);
        }

        return 0;
    }
}
