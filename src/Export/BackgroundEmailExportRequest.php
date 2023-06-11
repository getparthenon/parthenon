<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export;

use Symfony\Component\Security\Core\User\UserInterface;

class BackgroundEmailExportRequest extends ExportRequest
{
    protected UserInterface $user;

    public static function createFromExportRequest(ExportRequest $exportRequest, UserInterface $user): static
    {
        $self = new static($exportRequest->getName(), $exportRequest->getExportFormat(), $exportRequest->getDataProviderService(), $exportRequest->getDataProviderParameters());
        $self->user = $user;

        return $self;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
