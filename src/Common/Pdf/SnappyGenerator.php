<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
