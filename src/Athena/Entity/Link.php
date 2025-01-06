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
