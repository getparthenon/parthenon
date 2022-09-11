<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Command;

use Parthenon\AbTesting\Report\CleanUpSessions;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanUpCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'parthenon:ab-testing:cleanup';
    private CleanUpSessions $generator;

    public function __construct(CleanUpSessions $cleanUpSessions)
    {
        parent::__construct(null);
        $this->generator = $cleanUpSessions;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Clean up A/B sessions');
        $this->getLogger()->info('Clean up A/B sessions');

        $this->generator->cleanUp();

        return 0;
    }
}
