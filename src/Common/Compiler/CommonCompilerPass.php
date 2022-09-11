<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Compiler;

use Parthenon\Common\RequestHandler\RequestHandlerManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommonCompilerPass extends AbstractCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $this->handle($container, RequestHandlerManager::class, 'parthenon.common.request_handler', 'addRequestHandler');
    }
}
