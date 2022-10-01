<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncSubscriptionsCommand extends Command
{
    use LoggerAwareTrait;

    protected static $defaultName = 'parthenon:payments:sync-subscriptions';

    public function __construct(
        private SubscriberRepositoryInterface $subscriberRepository,
        private SubscriptionManagerInterface $subscriptionManager,
        private ToActiveManagerInterface $activeManager,
        private ToCancelledManagerInterface $cancelledManager,
        private ToOverdueManagerInterface $overdueManager,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(static::$defaultName)
            ->setDescription('Sync Subscriptions');
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
