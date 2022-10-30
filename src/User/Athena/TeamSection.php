<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
