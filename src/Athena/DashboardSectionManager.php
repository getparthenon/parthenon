<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Athena;

final class DashboardSectionManager
{
    /**
     * @var DashboardSectionInterface[]
     */
    private array $dashboardSections = [];

    public function add(DashboardSectionInterface $dashboardSection)
    {
        $this->dashboardSections[] = $dashboardSection;
    }

    /**
     * @return DashboardSectionInterface[]
     */
    public function getDashboardSections(): array
    {
        usort($this->dashboardSections, function (DashboardSectionInterface $dashboardSectionA, DashboardSectionInterface $dashboardSectionB) {
            return $dashboardSectionA->getPriority() <=> $dashboardSectionB->getPriority();
        });

        return array_filter($this->dashboardSections, function (DashboardSectionInterface $dashboardSection) {
            return $dashboardSection->isEnabled();
        });
    }
}
