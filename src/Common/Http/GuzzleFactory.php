<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

final class GuzzleFactory
{
    public static function build(): ClientInterface
    {
        return new Client();
    }
}
