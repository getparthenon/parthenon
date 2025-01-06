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

namespace Parthenon\Billing\Plan\Security\Voter;

use Parthenon\Billing\CustomerProviderInterface;
use Parthenon\Billing\Exception\NoCounterException;
use Parthenon\Billing\Plan\CounterManager;
use Parthenon\Billing\Plan\CustomerPlanInfoInterface;
use Parthenon\Billing\Plan\LimitableInterface;
use Parthenon\Billing\Plan\LimitedUserInterface;
use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PlanVoter extends Voter
{
    use LoggerAwareTrait;

    public const SUPPORTED_ATTRIBUTES = ['create', 'enable', 'enabled'];

    public function __construct(
        private CounterManager $counterManager,
        private CustomerProviderInterface $customerProvider,
        private CustomerPlanInfoInterface $customersPlanInfo,
    ) {
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

        if (!$subject instanceof LimitableInterface) {
            return true;
        }

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user instanceof LimitedUserInterface) {
            return true;
        }

        $subscriber = $this->customerProvider->getCurrentCustomer();

        $limit = $this->customersPlanInfo->getLimitCount($subscriber, $subject->getLimitableName());

        if (-1 === $limit) {
            return true;
        }

        try {
            $counter = $this->counterManager->getCounter($subject);
        } catch (NoCounterException $e) {
            $this->getLogger()->warning('No counter for limitable object found', ['limitable_class' => get_class($subject)]);

            return true;
        }

        return $counter->getCount($user, $subject) < $limit;
    }
}
