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
