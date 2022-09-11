<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Export;

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
