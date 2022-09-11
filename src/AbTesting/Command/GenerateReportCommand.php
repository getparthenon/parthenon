<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Command;

use Parthenon\AbTesting\Report\Generator;
use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateReportCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'parthenon:ab-testing:generate-report';
    private Generator $generator;

    public function __construct(Generator $generator)
    {
        parent::__construct(null);
        $this->generator = $generator;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Generate A/B report');
        $this->getLogger()->info('Generate A/B report');

        $this->generator->generate();

        return 0;
    }
}
