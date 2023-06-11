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

namespace Parthenon\AbTesting\Entity;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Variant
{
    protected UuidInterface $id;

    /**
     * @Assert\NotBlank
     */
    protected string $name;

    /**
     * @Assert\NotBlank
     * @Assert\LessThanOrEqual(100)
     */
    protected int $percentage;

    protected bool $isDefault = false;

    private Experiment $experiment;

    private VariantStats $stats;

    public function __construct()
    {
        $this->stats = new VariantStats();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): void
    {
        $this->percentage = $percentage;
    }

    public function isIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getExperiment(): Experiment
    {
        return $this->experiment;
    }

    public function setExperiment(Experiment $experiment): void
    {
        $this->experiment = $experiment;
    }

    public function getStats(): VariantStats
    {
        return $this->stats;
    }

    public function setStats(VariantStats $stats): void
    {
        $this->stats = $stats;
    }
}
