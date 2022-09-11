<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;

final class ExporterManager implements ExportManagerInterface
{
    /**
     * @var ExporterInterface[]
     */
    private array $exporters = [];

    public function add(ExporterInterface $exporter): void
    {
        $this->exporters[] = $exporter;
    }

    public function export(UserInterface $user): array
    {
        $output = [];

        foreach ($this->exporters as $exporter) {
            $name = $exporter->getName();
            $output[$name] = $exporter->export($user);
        }

        return $output;
    }
}
