<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Subscriptions\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\Subscriptions\Entity\Subscription;
use Parthenon\Subscriptions\Repository\SubscriberRepositoryInterface;
use Parthenon\Subscriptions\SubscriptionManagerInterface;
use Parthenon\Subscriptions\Transition\ToActiveManagerInterface;
use Parthenon\Subscriptions\Transition\ToCancelledManagerInterface;
use Parthenon\Subscriptions\Transition\ToOverdueManagerInterface;
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
