<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\User\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Creator\UserCreatorInterface;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'parthenon:user:create-user', description: 'Create user')]
final class CreateUserCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(private UserInterface $user, private UserCreatorInterface $userCreator)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address')
            ->addArgument('password', InputArgument::REQUIRED, 'The password');
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
            $input->setArgument('password', $email);
        }

        if (!$input->getArgument('password')) {
            $passwordQuestion = new Question('Please provide a password:');
            $passwordQuestion->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('password can not be empty');
                }

                return $password;
            });
            $passwordQuestion->setHidden(true);

            $password = $this->getHelper('question')->ask($input, $output, $passwordQuestion);
            $input->setArgument('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Create user command');
        $this->getLogger()->info('Create user command');

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $userClass = get_class($this->user);
        $user = new $userClass();
        $user->setEmail($email);
        $user->setPassword($password);

        $this->userCreator->create($user);

        return 0;
    }
}
