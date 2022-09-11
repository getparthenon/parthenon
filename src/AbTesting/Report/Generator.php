<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Exception\NoHandlerSetException;

final class Generator
{
    private GenerationHandlerInterface $generationHandler;

    public function __construct(private PdfGeneratorInterface $pdfGenerator)
    {
    }

    public function setGenerationHandler(GenerationHandlerInterface $generationHandler): void
    {
        $this->generationHandler = $generationHandler;
    }

    public function generate(): void
    {
        if (!isset($this->generationHandler)) {
            throw new NoHandlerSetException();
        }

        $pdf = $this->pdfGenerator->generate();
        $this->generationHandler->handle($pdf);
    }
}
