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

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\Pdf\GeneratorInterface;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class PdfGeneratorTest extends TestCase
{
    public function testCallsTwig()
    {
        $html = 'html here';
        $pdf = 'pdf content';
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $twig = $this->createMock(Environment::class);
        $generator = $this->createMock(GeneratorInterface::class);

        $experimentRepository->method('findAll')->will($this->returnCallback(function () {
            yield from [];
        }));

        $twig->method('render')->with('@Parthenon/abtesting/report.html.twig', $this->anything())->willReturn($html);

        $generator->method('generate')->with($html)->willReturn($pdf);

        $pdfGenerator = new PdfGenerator($experimentRepository, $twig, $generator);
        $this->assertEquals('pdf content', $pdfGenerator->generate());
    }
}
