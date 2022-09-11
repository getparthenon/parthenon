<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Notification\Slack;

interface WebhookPosterInterface
{
    public function send(string $webhook, array $message);
}
