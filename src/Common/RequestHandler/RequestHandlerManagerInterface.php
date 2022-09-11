<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use Parthenon\Common\Exception\RequestProcessor\NoValidRequestHandlerException;
use Symfony\Component\HttpFoundation\Request;

interface RequestHandlerManagerInterface
{
    /**
     * @throws NoValidRequestHandlerException
     */
    public function getRequestHandler(Request $request): RequestHandlerInterface;
}
