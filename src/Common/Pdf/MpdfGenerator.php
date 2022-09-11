<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Pdf;

use Mpdf\Mpdf;
use Parthenon\Common\Exception\GeneralException;

final class MpdfGenerator implements GeneratorInterface
{
    public function __construct(private Mpdf $mpdf)
    {
    }

    public function generate(string $html)
    {
        try {
            $this->mpdf->WriteHTML($html);

            return $this->mpdf->Output();
        } catch (\Exception $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
