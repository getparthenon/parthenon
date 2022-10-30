<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Gdpr\Deletion;

use Parthenon\User\Entity\UserInterface;

final class Decider implements VoterInterface
{
    /**
     * @var VoterInterface[]
     */
    private array $voters = [];

    public function add(VoterInterface $voter): self
    {
        $this->voters[] = $voter;

        return $this;
    }

    public function canDelete(UserInterface $user): bool
    {
        foreach ($this->voters as $voter) {
            if (!$voter->canDelete($user)) {
                return false;
            }
        }

        return true;
    }
}
