<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

final class DefaultAccessRights implements AccessRightsManagerInterface
{
    private const RIGHTS = [
        'create' => 'ROLE_USER',
        'view' => 'ROLE_USER',
        'delete' => 'ROLE_USER',
        'edit' => 'ROLE_USER',
    ];

    public function getAccessRights(SectionInterface $section): array
    {
        return array_merge(static::RIGHTS, $section->getAccessRights());
    }
}
