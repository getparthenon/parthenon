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

use PHPUnit\Framework\TestCase;

class CsvExporterTest extends TestCase
{
    public function testCsvExporterReturnsCorrectMimeType()
    {
        $exporter = new CsvExporter();

        $this->assertEquals('text/csv', $exporter->getMimeType());
    }

    public function testExportBasicArray()
    {
        $exporter = new CsvExporter();

        $expected = 'column_one,column_two,column_three'.PHP_EOL.
                    'value,value_two,'.PHP_EOL.
                    ',value_four,value_three'.PHP_EOL;

        $rows = [
          ['column_one' => 'value', 'column_two' => 'value_two'],
          ['column_three' => 'value_three', 'column_two' => 'value_four'],
        ];

        $this->assertEquals($expected, $exporter->getOutput($rows));
    }
}
