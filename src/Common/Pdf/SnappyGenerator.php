<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Common\Pdf;

use Knp\Snappy\Pdf;
use Parthenon\Common\Exception\GeneralException;

class SnappyGenerator implements GeneratorInterface
{
    private Pdf $pdf;

    public function __construct(string $bin)
    {
        $this->pdf = new Pdf($bin);
    }

    public function generate(string $html)
    {
        try {
            return $this->pdf->getOutputFromHtml($html);
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
