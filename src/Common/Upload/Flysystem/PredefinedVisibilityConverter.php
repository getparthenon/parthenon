<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Based on https://github.com/thephpleague/flysystem-aws-s3-v3/blob/2.x/PortableVisibilityConverter.php
 *
 * Copyright (c) 2014-2019 Frank de Jonge
 */

namespace Parthenon\Common\Upload\Flysystem;

use League\Flysystem\AsyncAwsS3\VisibilityConverter;
use League\Flysystem\Visibility;

final class PredefinedVisibilityConverter implements VisibilityConverter
{
    private const PUBLIC_GRANTEE_URI = 'http://acs.amazonaws.com/groups/global/AllUsers';
    private const PUBLIC_GRANTS_PERMISSION = 'READ';
    private const PUBLIC_ACL = 'public-read';
    private const PRIVATE_ACL = 'private';

    public function __construct(private string $visibility)
    {
    }

    public function visibilityToAcl(string $visibility): string
    {
        if (Visibility::PUBLIC === $this->visibility) {
            return self::PUBLIC_ACL;
        }

        return self::PRIVATE_ACL;
    }

    public function aclToVisibility(array $grants): string
    {
        foreach ($grants as $grant) {
            if (null === $grantee = $grant->getGrantee()) {
                continue;
            }
            $granteeUri = $grantee->getURI();
            $permission = $grant->getPermission();

            if (self::PUBLIC_GRANTEE_URI === $granteeUri && self::PUBLIC_GRANTS_PERMISSION === $permission) {
                return Visibility::PUBLIC;
            }
        }

        return Visibility::PRIVATE;
    }

    public function defaultForDirectories(): string
    {
        return $this->visibility;
    }
}
