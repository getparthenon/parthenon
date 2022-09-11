<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
final class UniqueUser extends Constraint
{
    public const IS_NOT_UNIQUE_USER = '525486d5-89b2-4017-92a0-fd34190b8b93';

    public string $message = 'The user should be unique.';
}
