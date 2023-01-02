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

namespace Parthenon\Funnel\Command;

use Parthenon\Funnel\Repository\RepositoryManager;
use Parthenon\Funnel\UnfinnishedActions\ActionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'parthenon:funnel:process-unfinished', description: 'Processes actions on unfinnished funnels')]
final class ProcessUnfinishedCommand extends Command
{
    private ActionManager $actionManager;
    private RepositoryManager $repositoryManager;
    private LoggerInterface $logger;

    public function __construct(ActionManager $actionManager, RepositoryManager $repositoryManager, LoggerInterface $logger)
    {
        $this->actionManager = $actionManager;
        $this->repositoryManager = $repositoryManager;
        parent::__construct();
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Starting to process unfinnished');
        $output->writeln('Starting to process unfinnished');
        foreach ($this->repositoryManager->getRepositories() as $repository) {
            $entites = $repository->getUnfinishedToBeProcessed();
            foreach ($entites as $entity) {
                foreach ($this->actionManager->getActions() as $action) {
                    if ($action->supports($entity)) {
                        $this->logger->info(
                            'Running unfinished funnels action on entity.',
                            [
                                'action_class' => get_class($action),
                                'entity_class' => get_class($entity),
                                'entity_id' => (string) $entity->getId(),
                            ]
                        );
                        $action->process($entity);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
