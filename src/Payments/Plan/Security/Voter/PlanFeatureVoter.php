<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
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

namespace Parthenon\Payments\Plan\Security\Voter;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Payments\Exception\NoPlanFoundException;
use Parthenon\Payments\Plan\CounterManager;
use Parthenon\Payments\Plan\LimitedUserInterface;
use Parthenon\Payments\Plan\PlanManagerInterface;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PlanFeatureVoter extends Voter
{
    use LoggerAwareTrait;

    public const SUPPORTED_ATTRIBUTES = ['feature_enabled'];
    private CounterManager $counterManager;
    private PlanManagerInterface $planManager;

    public function __construct(CounterManager $counterManager, PlanManagerInterface $planManager)
    {
        $this->counterManager = $counterManager;
        $this->planManager = $planManager;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, self::SUPPORTED_ATTRIBUTES)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!is_string($subject)) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof LimitedUserInterface) {
            return true;
        }

        try {
            $plan = $this->planManager->getPlanForUser($user);
        } catch (NoPlanFoundException $exception) {
            $this->getLogger()->warning('No plan for user', ['plan_name' => $user->getPlanName()]);

            return true;
        }

        return $plan->hasFeature($subject);
    }
}
