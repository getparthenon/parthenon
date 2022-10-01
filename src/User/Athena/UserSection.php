<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
