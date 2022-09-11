<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
