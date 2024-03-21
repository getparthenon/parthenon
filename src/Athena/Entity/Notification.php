<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\Athena\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity()
 *
 * @ORM\Table(name="parthenon_backoffice_notifications")
 */
class Notification
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(type="uuid", unique=true)
     *
     * @ORM\GeneratedValue(strategy="CUSTOM")
     *
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected string $messageTemplate;

    /**
     * @ORM\Embedded(class="Parthenon\Athena\Entity\Link")
     */
    protected Link $link;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected bool $isRead = false;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $readAt;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getMessageTemplate(): string
    {
        return $this->messageTemplate;
    }

    public function setMessageTemplate(string $messageTemplate): void
    {
        $this->messageTemplate = $messageTemplate;
    }

    public function getLink(): Link
    {
        return $this->link;
    }

    public function setLink(Link $link): void
    {
        $this->link = $link;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function setRead(bool $read): void
    {
        $this->isRead = $read;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTime $readAt): void
    {
        $this->readAt = $readAt;
    }

    public function markAsRead(): void
    {
        $this->isRead = true;
        $this->readAt = new \DateTime('now');
    }
}
