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

namespace Parthenon\Export\Exporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class XslExporter implements ExporterInterface
{
    public function getFormat(): string
    {
        return 'Xlsx';
    }

    public function getMimeType(): string
    {
        return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    }

    public function getFilename(string $name): string
    {
        return sprintf('%s.xlsx', $name);
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

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = array_keys($columns);
        $column = 'A';
        $rowCount = 1;
        foreach ($headers as $value) {
            $sheet->setCellValue($column.$rowCount, $value);
            ++$column;
        }
        ++$rowCount;
        foreach ($input as $row) {
            $csvRow = [];
            foreach ($row as $columnName => $value) {
                $index = $columns[$columnName];
                $csvRow[$index] = $value;
            }
            $outputRow = $this->populate($columns, $csvRow);
            $column = 'A';
            foreach ($outputRow as $value) {
                $sheet->setCellValue($column.$rowCount, $value);
                ++$column;
            }
            ++$rowCount;
        }

        $fp = fopen('php://memory', 'w');
        $writer = new Xls($spreadsheet);
        $writer->save($fp);
        fseek($fp, 0);

        return stream_get_contents($fp);
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
