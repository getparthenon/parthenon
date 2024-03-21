<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Parthenon\User\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\Settings;
use Parthenon\User\Controller\AthenaController;
use Parthenon\User\Entity\Team;
use Parthenon\User\Repository\TeamRepositoryInterface;

class TeamSection extends AbstractSection
{
    public function __construct(private TeamRepositoryInterface $teamRepository)
    {
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->teamRepository;
    }

    public function buildListView(ListView $listView): ListView
    {
        return $listView->addField('name', 'text', link: true);
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView->section('Team')
            ->field('name', 'text')
            ->end()
            ->section('Members', AthenaController::class.'::teamMembers')
            ->end();

        return $readView;
    }

    public function getEntity()
    {
        return new Team();
    }

    final public function getUrlTag(): string
    {
        return 'team';
    }

    public function getMenuSection(): string
    {
        return 'Users';
    }

    public function getMenuName(): string
    {
        return 'Teams';
    }

    public function getButtons(): array
    {
        return [];
    }

    public function getSettings(): Settings
    {
        return new Settings(['create' => false]);
    }
}
