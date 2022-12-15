<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 16.12.2025
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Notification;

use Parthenon\Notification\Email;
use Parthenon\User\Entity\UserInterface;

class UserEmail extends Email
{
    protected ?UserInterface $user;

    public static function createFromUser(UserInterface $user): self
    {
        $self = new UserEmail();
        $self->setToName($user->getName())->setToAddress($user->getEmail());
        $self->user = $user;

        return $self;
    }

    public function getTemplateVariables(): array
    {
        return array_merge(parent::getTemplateVariables(), ['name' => $this->user->getName()]);
    }
}
