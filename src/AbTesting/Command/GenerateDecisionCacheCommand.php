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

namespace Parthenon\AbTesting\Command;

use Parthenon\AbTesting\Decider\ChoiceDecider\CacheGenerator;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:ab-testing:generate-decision-cache')]
final class GenerateDecisionCacheCommand extends Command
{
    use LoggerAwareTrait;

    private CacheGenerator $generator;

    public function __construct(CacheGenerator $generator)
    {
        parent::__construct(null);
        $this->generator = $generator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating ab testing decision cache');
        $this->getLogger()->info('Generating ab testing decision cache');

        $this->generator->generate();

        return 0;
    }
}
