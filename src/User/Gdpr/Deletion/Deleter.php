<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\UserInterface;

final class Deleter implements DeleterInterface
{
    /**
     * @var DeleterInterface[]
     */
    private array $deleters = [];

    public function add(DeleterInterface $deleter): self
    {
        $this->deleters[] = $deleter;

        return $this;
    }

    public function delete(UserInterface $user)
    {
        foreach ($this->deleters as $deleter) {
            $deleter->delete($user);
        }
    }
}
