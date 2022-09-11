<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestProcessor implements ProcessorInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(array $record): array
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            return $record;
        }

        $record['extra']['request_uri'] = $request->getUri();
        $record['extra']['request_method'] = $request->getRealMethod();

        return $record;
    }
}
