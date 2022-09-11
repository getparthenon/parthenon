<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification;

final class Attachment
{
    private string $name;

    private $content;

    public function __construct(string $name, $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
