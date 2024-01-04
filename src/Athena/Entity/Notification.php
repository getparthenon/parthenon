<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2024
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity()
 * @ORM\Table(name="parthenon_backoffice_notifications")
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
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
