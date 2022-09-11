<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
