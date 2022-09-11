<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
