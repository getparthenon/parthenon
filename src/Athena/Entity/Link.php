<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Link
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private string $urlName;

    /**
     * @ORM\Column(type="json", nullable=false)
     */
    private array $urlVariables = [];

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $isRaw = false;

    public function __construct(string $urlName = '', array $urlVariables = [], bool $isRaw = false)
    {
        $this->urlName = $urlName;
        $this->urlVariables = $urlVariables;
        $this->isRaw = $isRaw;
    }

    public function getUrlName(): string
    {
        return $this->urlName;
    }

    public function setUrlName(string $urlName): void
    {
        $this->urlName = $urlName;
    }

    public function getUrlVariables(): array
    {
        return $this->urlVariables;
    }

    public function setUrlVariables(array $urlVariables): void
    {
        $this->urlVariables = $urlVariables;
    }

    public function isRaw(): bool
    {
        return $this->isRaw;
    }
}
