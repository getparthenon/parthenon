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

namespace Parthenon\Export\Entity;

use Parthenon\Export\ExportRequest;

class BackgroundExportRequest extends ExportRequest
{
    private $id;

    private ?string $exportedFile = null;

    private ?string $exportedFilePath = null;

    private \DateTimeInterface $createdAt;

    private \DateTimeInterface $updatedAt;

    public static function createFromExportRequest(ExportRequest $request)
    {
        $self = new static($request->getName(), $request->getExportFormat(), $request->getDataProviderService(), $request->getDataProviderParameters());
        $now = new \DateTime();
        $self->createdAt = $now;
        $self->updatedAt = $now;

        return $self;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getExportedFile(): ?string
    {
        return $this->exportedFile;
    }

    public function setExportedFilePath(string $exportFilePath): void
    {
        $this->exportedFilePath = $exportFilePath;
    }

    public function getExportedFilePath(): ?string
    {
        return $this->exportedFilePath;
    }

    public function setExportedFile(?string $exportedFile): void
    {
        $this->exportedFile = $exportedFile;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
