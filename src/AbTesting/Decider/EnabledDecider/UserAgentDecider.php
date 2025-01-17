<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
