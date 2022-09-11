<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Invoice;

use Parthenon\Common\Pdf\GeneratorInterface;
use Parthenon\Invoice\Invoice;
use Parthenon\Invoice\PdfExporter;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class PdfExporterTest extends TestCase
{
    public const TEMPLATE = 'template';

    public function testCallsTwig()
    {
        $generator = $this->createMock(GeneratorInterface::class);
        $twig = $this->createMock(Environment::class);

        $invoice = new Invoice();

        $twig->method('render')->with(self::TEMPLATE, ['invoice' => $invoice])->willReturn('HTML');
        $generator->method('generate')->willReturn('HTML')->willReturn('PDF');

        $pdfExporter = new PdfExporter($generator, $twig);
        $this->assertEquals('PDF', $pdfExporter->exportPdf($invoice, self::TEMPLATE));
    }
}
