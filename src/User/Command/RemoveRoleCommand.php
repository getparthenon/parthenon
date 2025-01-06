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

namespace Parthenon\User\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'parthenon:user:remove-role', description: 'Remove role')]
final class RemoveRoleCommand extends Command
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Remove role command');
        $this->getLogger()->info('Remove role  command');

        $email = $input->getArgument('email');
        $role = $input->getArgument('role');

        $user = $this->userRepository->findByEmail($email);

        $user->setRoles(array_filter($user->getRoles(), function ($v) use ($role) {
            return $v !== $role;
        }));

        $this->userRepository->save($user);

        return 0;
    }
}
