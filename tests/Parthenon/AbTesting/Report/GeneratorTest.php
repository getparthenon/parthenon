<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
