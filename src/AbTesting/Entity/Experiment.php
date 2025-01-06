<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2025 Iain Cambridge
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

namespace Parthenon\AbTesting\Entity;

use Parthenon\Athena\Entity\DeletableInterface;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Experiment implements DeletableInterface
{
    protected UuidInterface $id;

    protected ?\DateTimeInterface $deletedAt;

    protected $isDeleted = false;

    /**
     * @Assert\NotBlank
     */
    private string $type;

    /**
     * @Assert\NotBlank
     */
    private string $name;

    /**
     * @Assert\NotBlank
     */
    private string $desiredResult;

    private \DateTime $createdAt;

    private ?\DateTime $updatedAt;

    private $variants = [];

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(\DateTimeInterface $dateTime): DeletableInterface
    {
        $this->deletedAt = $dateTime;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function markAsDeleted(): DeletableInterface
    {
        $this->isDeleted = true;
        $this->deletedAt = new \DateTime('Now');

        return $this;
    }

    public function unmarkAsDeleted(): DeletableInterface
    {
        $this->isDeleted = false;
        $this->deletedAt = null;

        return $this;
    }

    public function getDesiredResult(): string
    {
        return $this->desiredResult;
    }

    public function setDesiredResult(string $desiredResult): void
    {
        $this->desiredResult = $desiredResult;
    }

    /**
     * @return Variant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    public function setVariants($variants): void
    {
        $this->variants = $variants;
    }

    /**
     * @Assert\EqualTo(100, message="Variant percentages should equal 100")
     */
    public function getTotalPercentage(): int
    {
        $total = 0;
        /** @var Variant $variant */
        foreach ($this->variants as $variant) {
            $total += $variant->getPercentage();
        }

        return $total;
    }

    /**
     * @Assert\LessThanOrEqual(1, message="There can only be one default variant")
     */
    public function getNumberOfDefaultVariants(): int
    {
        $count = 0;
        foreach ($this->variants as $variant) {
            if ($variant->isIsDefault()) {
                ++$count;
            }
        }

        return $count;
    }

    public function isPredecided(): bool
    {
        foreach ($this->variants as $variant) {
            if ($variant->isIsDefault()) {
                return true;
            }
        }

        return false;
    }

    public function isUserBased(): bool
    {
        return 'user' === $this->type;
    }
}
