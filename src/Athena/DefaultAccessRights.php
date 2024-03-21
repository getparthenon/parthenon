<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena;

final class DefaultAccessRights implements AccessRightsManagerInterface
{
    private const RIGHTS = [
        'create' => 'ROLE_USER',
        'view' => 'ROLE_USER',
        'delete' => 'ROLE_USER',
        'edit' => 'ROLE_USER',
        'export' => 'ROLE_USER',
    ];

    public function getAccessRights(SectionInterface $section): array
    {
        return array_merge(static::RIGHTS, $section->getAccessRights());
    }
}
