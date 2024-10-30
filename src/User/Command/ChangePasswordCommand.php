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

namespace Parthenon\User\Command;

use Parthenon\Common\LoggerAwareTrait;
use Parthenon\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

#[AsCommand(name: 'parthenon:user:change-password', description: 'Change user password')]
final class ChangePasswordCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(private UserRepositoryInterface $userRepository, private PasswordHasherFactoryInterface $hasherFactory)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password');
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

        if (!$input->getArgument('password')) {
            $passwordQuestion = new Question('Please provide a password:');
            $passwordQuestion->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('password can not be empty');
                }

                return $password;
            });
            $passwordQuestion->setHidden(false);

            $password = $this->getHelper('question')->ask($input, $output, $passwordQuestion);
            $input->setArgument('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Change password command');
        $this->getLogger()->info('Change password command');

        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = $this->userRepository->findByEmail($email);

        $hasher = $this->hasherFactory->getPasswordHasher($user);
        $user->setPassword($hasher->hash($password));

        $this->userRepository->save($user);

        return 0;
    }
}
