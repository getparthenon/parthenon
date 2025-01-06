<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2025.
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
