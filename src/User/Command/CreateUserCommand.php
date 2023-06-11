<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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

    protected function execute(InputInterface $input, OutputInterface $output)
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
