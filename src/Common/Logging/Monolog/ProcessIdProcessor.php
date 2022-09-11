<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;

final class ProcessIdProcessor implements ProcessorInterface
{
    private ProcessIdGenerator $processIdGenerator;

    public function __construct(ProcessIdGenerator $processIdGenerator)
    {
        $this->processIdGenerator = $processIdGenerator;
    }

    public function __invoke(array $record): array
    {
        $record['extra']['process_id'] = $this->processIdGenerator->getProcessId();

        return $record;
    }
}
