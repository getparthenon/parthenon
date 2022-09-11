<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;

interface ExportManagerInterface
{
    public function add(ExporterInterface $exporter): void;

    public function export(UserInterface $user): array;
}
