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

namespace Parthenon\Payments\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Payments\Entity\Subscription;
use Parthenon\Payments\Repository\SubscriberRepositoryInterface;
use Parthenon\Payments\SubscriptionManagerInterface;
use Parthenon\Payments\Transition\ToActiveManagerInterface;
use Parthenon\Payments\Transition\ToCancelledManagerInterface;
use Parthenon\Payments\Transition\ToOverdueManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:payments:sync-subscriptions', description: 'Sync Subscriptions')]
class SyncSubscriptionsCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(
        private SubscriberRepositoryInterface $subscriberRepository,
        private SubscriptionManagerInterface $subscriptionManager,
        private ToActiveManagerInterface $activeManager,
        private ToCancelledManagerInterface $cancelledManager,
        private ToOverdueManagerInterface $overdueManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscribers = $this->subscriberRepository->findAllSubscriptions();

        foreach ($subscribers as $subscriber) {
            $oldStatus = $subscriber->getSubscription()->getStatus();

            $this->subscriptionManager->syncStatus($subscriber->getSubscription());

            if ($oldStatus !== $subscriber->getSubscription()->getStatus()) {
                switch ($subscriber->getSubscription()->getStatus()) {
                    case Subscription::STATUS_ACTIVE:
                        $this->activeManager->transition($subscriber);
                        break;
                    case Subscription::STATUS_CANCELLED:
                        $this->cancelledManager->transition($subscriber);
                        break;
                    case Subscription::STATUS_OVERDUE:
                        $this->cancelledManager->transition($subscriber);
                        break;
                }
            }

            $this->subscriberRepository->save($subscriber);
        }

        return Command::SUCCESS;
    }
}
