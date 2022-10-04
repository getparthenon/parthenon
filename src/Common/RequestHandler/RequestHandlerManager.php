<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
