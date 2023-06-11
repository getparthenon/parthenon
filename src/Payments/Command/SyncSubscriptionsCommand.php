<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
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
