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
