<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

interface AccessRightsManagerInterface
{
    public function getAccessRights(SectionInterface $section): array;
}
