<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Athena\Dashboard;

use Parthenon\Athena\AbstractDashboardSection;
use Parthenon\User\Repository\UserRepositoryInterface;

final class SignedUpCountSection extends AbstractDashboardSection
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function getTitle(): string
    {
        return 'parthenon.user.athena.dashboard.signup_stats.title';
    }

    public function getTemplate(): string
    {
        return '@Parthenon/user/athena/dashboard/signedup.html.twig';
    }

    public function getTemplateData(): array
    {
        return $this->userRepository->getUserSignupStats();
    }
}
