<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
