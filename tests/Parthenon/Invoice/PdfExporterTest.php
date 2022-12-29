<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Invoice;

use Parthenon\Common\Pdf\GeneratorInterface;
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
