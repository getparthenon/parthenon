<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
