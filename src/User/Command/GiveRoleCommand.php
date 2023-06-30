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

namespace Parthenon\User\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'parthenon:user:give-role', description: 'Give role')]
final class GiveRoleCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(private UserRepositoryInterface $userRepository)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address')
            ->addArgument('role', InputArgument::REQUIRED, 'The role');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $emailQuestion = new Question('Please provide an email:');
            $emailQuestion->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });

            $email = $this->getHelper('question')->ask($input, $output, $emailQuestion);

            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('role')) {
            $roleQuestion = new Question('Please provide a role:');
            $roleQuestion->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('role can not be empty');
                }

                return $password;
            });
            $roleQuestion->setHidden(false);

            $role = $this->getHelper('question')->ask($input, $output, $roleQuestion);
            $input->setArgument('role', $role);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Give role command');
        $this->getLogger()->info('Give role  command');

        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->userRepository->findByEmail($email);
        $user->setRoles(array_merge([$role], $user->getRoles()));

        $this->userRepository->save($user);

        return 0;
    }
}
