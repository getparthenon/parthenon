<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Funnel\Command;

use Parthenon\Funnel\Repository\RepositoryManager;
use Parthenon\Funnel\UnfinnishedActions\ActionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

    protected function configure()
    {
        $this->setName('parthenon:funnel:process-unfinished')
        ->setDescription('Processes actions on unfinnished funnels');
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
