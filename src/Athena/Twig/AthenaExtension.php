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

namespace Parthenon\Athena\Twig;

use Parthenon\Athena\Filters\ListFiltersInterface;
use Parthenon\Athena\Repository\NotificationRepositoryInterface;
use Parthenon\Athena\SectionManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class AthenaExtension extends AbstractExtension
{
    private SectionManager $sectionManager;

    private NotificationRepositoryInterface $notificationRepository;
    private ?string $loginLogo;
    private ?string $dashboardLogo;

    public function __construct(SectionManager $sectionManager, NotificationRepositoryInterface $notificationRepository, ?string $loginLogo, ?string $dashboardLogo)
    {
        $this->sectionManager = $sectionManager;
        $this->notificationRepository = $notificationRepository;
        $this->loginLogo = $loginLogo;
        $this->dashboardLogo = $dashboardLogo;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('count', 'count'),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('athena_menu', [$this->sectionManager, 'getMenu']),
            new TwigFunction('athena_notifications', [$this->notificationRepository, 'getAllUnread']),
            new TwigFunction('athena_crud_filters_link', [$this, 'generateQueryString']),
            new TwigFunction('athena_login_logo', [$this, 'getLoginLogo']),
            new TwigFunction('athena_dashboard_logo', [$this, 'getDashboardLogo']),
        ];
    }

    public function generateQueryString(ListFiltersInterface $listFilters): string
    {
        $output = '';
        foreach ($listFilters->getFilters() as $filter) {
            if ($filter->hasData()) {
                $output .= '&filters['.$filter->getFieldName().']='.$filter->getData();
            }
        }

        return $output;
    }

    public function getLoginLogo(): ?string
    {
        return $this->loginLogo;
    }

    public function getDashboardLogo(): ?string
    {
        return $this->dashboardLogo;
    }
}
