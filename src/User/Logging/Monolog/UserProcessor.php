<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Logging\Monolog;

use Monolog\Processor\ProcessorInterface;
use Parthenon\User\Entity\UserInterface;
use Symfony\Component\Security\Core\Security;

final class UserProcessor implements ProcessorInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(array $record): array
    {
        /**
         * @var UserInterface|null
         */
        $user = $this->security->getUser();
        if (!$user) {
            return $record;
        }

        $record['extra']['user_id'] = (string) $user->getId();

        return $record;
    }
}
