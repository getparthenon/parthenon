<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\AbTesting\Decider\EnabledDecider;

use Parthenon\AbTesting\Decider\EnabledDeciderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class UserAgentDecider implements EnabledDeciderInterface
{
    private RequestStack $requestStack;
    private array $userAgents;

    public function __construct(RequestStack $requestStack, array $userAgents)
    {
        $this->requestStack = $requestStack;
        $this->userAgents = $userAgents;
    }

    public function isTestable(): bool
    {
        $actualUserAgent = $this->requestStack->getCurrentRequest()->headers->get('User-Agent');

        if (!$actualUserAgent) {
            return false;
        }

        foreach ($this->userAgents as $userAgent) {
            if (preg_match('~'.$userAgent.'~', $actualUserAgent)) {
                return false;
            }
        }

        return true;
    }
}
