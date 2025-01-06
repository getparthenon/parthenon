<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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
