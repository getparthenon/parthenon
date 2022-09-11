<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
