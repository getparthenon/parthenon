<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Event;

use Parthenon\User\Entity\ForgotPasswordCode;
use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PrePasswordResetEvent extends Event
{
    public const NAME = 'parthenon.user.password_reset.pre';
    private UserInterface $user;
    private ForgotPasswordCode $passwordReset;

    public function __construct(UserInterface $user, ForgotPasswordCode $passwordReset)
    {
        $this->user = $user;
        $this->passwordReset = $passwordReset;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getPasswordReset(): ForgotPasswordCode
    {
        return $this->passwordReset;
    }
}
