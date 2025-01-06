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

    protected function execute(InputInterface $input, OutputInterface $output): int
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
