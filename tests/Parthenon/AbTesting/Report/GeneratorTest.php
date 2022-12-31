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
