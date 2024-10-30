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

namespace Parthenon\User\Athena;

use Parthenon\Athena\AbstractSection;
use Parthenon\Athena\Button;
use Parthenon\Athena\EntityForm;
use Parthenon\Athena\Filters\ListFilters;
use Parthenon\Athena\ListView;
use Parthenon\Athena\ReadView;
use Parthenon\Athena\Repository\CrudRepositoryInterface;
use Parthenon\Athena\Settings;
use Parthenon\User\Entity\User;
use Parthenon\User\Repository\UserRepositoryInterface;

class UserSection extends AbstractSection
{
    public function __construct(private UserRepositoryInterface $userRepository, private array $roles = [])
    {
    }

    public function getEntity()
    {
        return new User();
    }

    public function getRepository(): CrudRepositoryInterface
    {
        return $this->userRepository;
    }

    public function buildListView(ListView $listView): ListView
    {
        $listView->addField('id', 'text', false, true)
            ->addField('name', 'text', true, true)
            ->addField('email', 'text', true)
            ->addField('is_confirmed', 'boolean', true);

        return $listView;
    }

    public function buildFilters(ListFilters $listFilters): ListFilters
    {
        $listFilters->add('name')
            ->add('email');

        return $listFilters;
    }

    public function buildReadView(ReadView $readView): ReadView
    {
        $readView->section('user_information')
                ->field('name')
                ->field('email')
            ->end()
            ->section('roles')
                ->field('roles', 'text')
            ->end();

        return $readView;
    }

    public function buildEntityForm(EntityForm $entityForm): EntityForm
    {
        $entityForm->section('user_information')
                ->field('name', 'text')
                ->field('email', 'text')
            ->end()
            ->section('roles')
                ->field('roles', 'choice', ['choices' => $this->roles, 'multiple' => true, 'expanded' => true])
            ->end();

        return $entityForm;
    }

    final public function getUrlTag(): string
    {
        return 'user';
    }

    public function getMenuSection(): string
    {
        return 'Users';
    }

    public function getMenuName(): string
    {
        return 'Manage';
    }

    public function getRelatedPages(): array
    {
        return [];
    }

    public function getSettings(): Settings
    {
        return new Settings([]);
    }

    public function getButtons(): array
    {
        return [
            new Button('parthenon_athena_user_gdpr_export', 'parthenon.user.athena.gdpr.export', 'parthenon_athena_user_gdpr_export'),
        ];
    }

    public function getAccessRights(): array
    {
        return [];
    }
}
