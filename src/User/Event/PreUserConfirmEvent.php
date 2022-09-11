<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PreUserConfirmEvent extends Event
{
    public const NAME = 'parthenon.user.confirm.pre';
    private UserInterface $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
