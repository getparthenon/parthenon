<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Report;

use Parthenon\AbTesting\Repository\ExperimentRepositoryInterface;
use Parthenon\Common\Pdf\GeneratorInterface;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class PdfGeneratorTest extends TestCase
{
    public function testCallsTwig()
    {
        $html = 'html here';
        $pdf = 'pdf content';
        $experimentRepository = $this->createMock(ExperimentRepositoryInterface::class);
        $twig = $this->createMock(Environment::class);
        $generator = $this->createMock(GeneratorInterface::class);

        $experimentRepository->method('findAll')->will($this->returnCallback(function () {
            yield from [];
        }));

        $twig->method('render')->with('@Parthenon/abtesting/report.html.twig', $this->anything())->willReturn($html);

        $generator->method('generate')->with($html)->willReturn($pdf);

        $pdfGenerator = new PdfGenerator($experimentRepository, $twig, $generator);
        $this->assertEquals('pdf content', $pdfGenerator->generate());
    }
}
