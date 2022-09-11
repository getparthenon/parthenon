<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Security\UserChecker;

use Symfony\Component\Security\Core\User\UserCheckerInterface;

final class UserCheckerObserver implements UserCheckerInterface
{
    /**
     * @var UserCheckerInterface[]
     */
    private array $checkers = [];

    public function add(UserCheckerInterface $userChecker)
    {
        $this->checkers[] = $userChecker;
    }

    public function checkPreAuth(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        foreach ($this->checkers as $checker) {
            $checker->checkPreAuth($user);
        }
    }

    public function checkPostAuth(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        foreach ($this->checkers as $checker) {
            $checker->checkPostAuth($user);
        }
    }
}
