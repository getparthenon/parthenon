<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\MultiTenancy\Event;

use Parthenon\MultiTenancy\Entity\TenantInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class TenantCreatedEvent extends Event
{
    public const NAME = 'parthenon.multi_tenancy.tenant_created';

    public function __construct(private TenantInterface $tenant)
    {
    }

    public function getTenant(): TenantInterface
    {
        return $this->tenant;
    }
}
