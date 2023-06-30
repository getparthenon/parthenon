<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
