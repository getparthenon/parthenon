<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\RequestHandler;

use Parthenon\Common\Exception\RequestProcessor\NoValidRequestHandlerException;
use Symfony\Component\HttpFoundation\Request;

final class RequestHandlerManager implements RequestHandlerManagerInterface
{
    /**
     * @var RequestHandlerInterface[]
     */
    private array $requestHandlers = [];

    public function addRequestHandler(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandlers[] = $requestHandler;
    }

    public function getRequestHandler(Request $request): RequestHandlerInterface
    {
        foreach ($this->requestHandlers as $requestHandler) {
            if ($requestHandler->supports($request)) {
                return $requestHandler;
            }
        }

        throw new NoValidRequestHandlerException();
    }
}
