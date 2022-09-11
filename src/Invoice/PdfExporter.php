<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Invoice;

use Parthenon\Common\Pdf\GeneratorInterface;
use Twig\Environment;

final class PdfExporter
{
    private GeneratorInterface $generator;

    private Environment $twig;

    public function __construct(GeneratorInterface $generator, Environment $twig)
    {
        $this->generator = $generator;
        $this->twig = $twig;
    }

    public function exportPdf(Invoice $invoice, string $templateName): string
    {
        $html = $this->twig->render($templateName, ['invoice' => $invoice]);

        return $this->generator->generate($html);
    }
}
