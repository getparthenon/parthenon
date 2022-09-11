<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Entity;

interface TeamInterface
{
    public function getId();

    public function addMember(MemberInterface $member): self;

    public function hasMember(MemberInterface $member): bool;

    public function getMembers(): array;

    public function getTeamSize(): int;

    public function setCreatedAt(\DateTime $createdAt): self;

    public function setName(?string $name);

    public function getName(): ?string;
}
