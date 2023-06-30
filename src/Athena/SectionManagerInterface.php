<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena;

use Parthenon\Athena\Controller\AthenaControllerInterface;
use Parthenon\Athena\Exception\NoSectionFoundException;

interface SectionManagerInterface
{
    public function addSection(SectionInterface $section): SectionManager;

    public function addController(AthenaControllerInterface $backofficeController): SectionManager;

    /**
     * @return SectionInterface[]
     */
    public function getSections(): array;

    /**
     * @return AthenaControllerInterface[]
     */
    public function getControllers(): array;

    /**
     * @throws NoSectionFoundException
     */
    public function getByUrlTag(string $urlTag): SectionInterface;

    /**
     * @throws \InvalidArgumentException
     * @throws NoSectionFoundException
     */
    public function getByEntity(mixed $entity): SectionInterface;

    public function getMenu(): array;
}
