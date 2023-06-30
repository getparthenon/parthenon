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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generating ab testing decision cache');
        $this->getLogger()->info('Generating ab testing decision cache');

        $this->generator->generate();

        return 0;
    }
}
