<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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
