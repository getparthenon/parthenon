<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
