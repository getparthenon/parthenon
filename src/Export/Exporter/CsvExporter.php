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
