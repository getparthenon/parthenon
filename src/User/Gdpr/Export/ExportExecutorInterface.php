<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;

interface ExportExecutorInterface
{
    public function export(UserInterface $user): Response;
}
