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

namespace Parthenon\MultiTenancy\Messenger;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\MultiTenancy\Creator\TenantCreatorInterface;
use Parthenon\MultiTenancy\Entity\TenantInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TenantCreatorHandler implements MessageHandlerInterface
{
    use LoggerAwareTrait;

    public function __construct(private TenantCreatorInterface $tenantCreator)
    {
    }

    public function __invoke(TenantInterface $message)
    {
        $this->getLogger()->info('Creating tenant from Message');

        $this->tenantCreator->createTenant($message);
    }
}
