<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Pdf;

use Mpdf\Mpdf;
use PHPUnit\Framework\TestCase;

class MpdfGeneratorTest extends TestCase
{
    public function testCallsMpdf()
    {
        $html = '<html><body>Hello World</body></html>';
        $mpdf = $this->createMock(Mpdf::class);
        $mpdf->expects($this->once())->method('WriteHTML')->with($html);

        $generator = new MpdfGenerator($mpdf);
        $generator->generate($html);
    }
}
