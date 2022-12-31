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
