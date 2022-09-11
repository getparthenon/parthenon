<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
