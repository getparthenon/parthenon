<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
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

    protected function execute(InputInterface $input, OutputInterface $output)
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
