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

namespace Parthenon\User\Event;

use Parthenon\User\Entity\ForgotPasswordCode;
use Parthenon\User\Entity\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class PostPasswordResetEvent extends Event
{
    public const NAME = 'parthenon.user.password_reset.post';
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
