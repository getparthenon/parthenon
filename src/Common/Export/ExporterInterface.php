<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Export;

interface ExporterInterface
{
    public function getMimeType(): string;

    public function getOutput(array $input): mixed;
}
