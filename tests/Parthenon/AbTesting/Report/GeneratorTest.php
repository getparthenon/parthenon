<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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

use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function testCallsPdfGeneratorThenHandler()
    {
        $pdfGenerator = $this->createMock(PdfGeneratorInterface::class);
        $pdfGenerator->method('generate')->willReturn('content');

        $generationHandler = $this->createMock(GenerationHandlerInterface::class);
        $generationHandler->expects($this->once())->method('handle')->with('content');

        $generator = new Generator($pdfGenerator);
        $generator->setGenerationHandler($generationHandler);
        $generator->generate();
    }
}
