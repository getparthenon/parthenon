<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
