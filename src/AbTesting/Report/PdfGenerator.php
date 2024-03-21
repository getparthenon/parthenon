<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\Pdf\GeneratorInterface;
use Twig\Environment;

final class PdfGenerator implements PdfGeneratorInterface
{
    private ExperimentRepositoryInterface $experimentRepository;
    private Environment $environment;
    private ?GeneratorInterface $generator;

    public function __construct(ExperimentRepositoryInterface $experimentRepository, Environment $environment, ?GeneratorInterface $generator)
    {
        $this->experimentRepository = $experimentRepository;
        $this->environment = $environment;
        $this->generator = $generator;
    }

    public function generate(): string
    {
        if (!$this->generator instanceof GeneratorInterface) {
            throw new \Exception('No PDF generator set');
        }

        $experiments = $this->experimentRepository->findAll();
        $html = $this->environment->render('@Parthenon/abtesting/report.html.twig', ['experiments' => $experiments]);

        return $this->generator->generate($html);
    }
}
