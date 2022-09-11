<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
