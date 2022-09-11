<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
