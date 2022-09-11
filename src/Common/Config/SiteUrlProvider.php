<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Config;

class SiteUrlProvider implements SiteUrlProviderInterface
{
    public function __construct(private string $siteUrl)
    {
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }
}
