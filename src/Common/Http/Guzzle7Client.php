<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Http;

use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Guzzle7Client implements ClientInterface
{
    private GuzzleClientInterface $client;

    public function __construct(GuzzleClientInterface $client)
    {
        $this->client = $client;
    }

    public function sendRequest(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->client->send($request, $options);
    }
}
