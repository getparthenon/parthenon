<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation, either version 2.1 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
