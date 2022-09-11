<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Gdpr\Export;

use Parthenon\User\Entity\UserInterface;

interface FormatterInterface
{
    public function getName(): string;

    public function getFilename(UserInterface $user): string;

    public function format(array $data): string;
}
