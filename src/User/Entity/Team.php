<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Team implements TeamInterface
{
    protected $id;
    protected Collection $members;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    private ?\DateTime $deletedAt;
    private ?string $name = '';

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MemberInterface[]
     */
    public function getMembers(): array
    {
        return $this->members->getValues();
    }

    public function getTeamSize(): int
    {
        return count($this->members);
    }

    public function hasMember(MemberInterface $member): bool
    {
        return $this->members->contains($member);
    }

    public function addMember(MemberInterface $member): TeamInterface
    {
        $this->members->add($member);

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
