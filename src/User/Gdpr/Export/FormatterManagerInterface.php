<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;
use Symfony\Component\HttpFoundation\Response;

interface FormatterManagerInterface
{
    public function add(FormatterInterface $formatter): void;

    public function format(UserInterface $user, array $data): Response;
}
