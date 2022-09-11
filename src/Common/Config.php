<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common;

use Parthenon\Common\Config\SiteUrlProviderInterface;

final class Config
{
    public function __construct(private SiteUrlProviderInterface $siteUrlProvider)
    {
    }

    public function getSiteUrl(): string
    {
        return $this->siteUrlProvider->getSiteUrl();
    }
}
