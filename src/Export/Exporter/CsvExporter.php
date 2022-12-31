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

namespace Parthenon\Export\Exporter;

class CsvExporter implements ExporterInterface
{
    public const EXPORT_FORMAT = 'csv';

    public function getMimeType(): string
    {
        return 'text/csv';
    }

    public function getOutput(array $input): mixed
    {
        $columns = [];
        $index = 0;
        foreach ($input as $row) {
            foreach ($row as $key => $value) {
                if (!isset($columns[$key])) {
                    $columns[$key] = $index;
                    ++$index;
                }
            }
        }

        $fp = fopen('php://memory', 'w');
        fputcsv($fp, array_keys($columns));

        foreach ($input as $row) {
            $csvRow = [];
            foreach ($row as $columnName => $value) {
                $index = $columns[$columnName];
                $csvRow[$index] = $value;
            }
            $outputRow = $this->populate($columns, $csvRow);
            fputcsv($fp, $outputRow);
        }

        fseek($fp, 0);

        return stream_get_contents($fp);
    }

    public function getFilename(string $name): string
    {
        return sprintf('%s.csv', $name);
    }

    public function getFormat(): string
    {
        return self::EXPORT_FORMAT;
    }

    private function populate(array $columns, array $row): array
    {
        foreach ($columns as $columnName => $key) {
            if (!isset($row[$key])) {
                $row[$key] = null;
            }
        }
        ksort($row);

        return $row;
    }
}
