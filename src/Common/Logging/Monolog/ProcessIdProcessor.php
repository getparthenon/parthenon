<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
