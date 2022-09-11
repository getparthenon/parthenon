<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
