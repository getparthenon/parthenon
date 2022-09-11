<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
