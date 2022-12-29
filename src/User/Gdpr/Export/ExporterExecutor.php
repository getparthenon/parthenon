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

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;

final class ExporterExecutor implements ExportExecutorInterface
{
    private ExportManagerInterface $exporterExecutor;
    private FormatterManagerInterface $formatterExecutor;

    public function __construct(ExportManagerInterface $exporterExecutor, FormatterManagerInterface $formatterExecutor)
    {
        $this->exporterExecutor = $exporterExecutor;
        $this->formatterExecutor = $formatterExecutor;
    }

    public function export(UserInterface $user): Response
    {
        $data = $this->exporterExecutor->export($user);

        return $this->formatterExecutor->format($user, $data);
    }
}
