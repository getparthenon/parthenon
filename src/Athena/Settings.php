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

namespace Parthenon\Athena;

final class Settings
{
    private array $setting;

    public function __construct(array $setting)
    {
        $this->setting = $setting;
    }

    public function isReadEnabled(): bool
    {
        if (array_key_exists('read', $this->setting) && false === $this->setting['read']) {
            return false;
        }

        return true;
    }

    public function isExportEnabled(): bool
    {
        if (array_key_exists('export', $this->setting) && false === $this->setting['export']) {
            return false;
        }

        return true;
    }

    public function hasSavedFilters(): bool
    {
        if (array_key_exists('saved_filters', $this->setting) && false !== $this->setting['saved_filters']) {
            return true;
        }

        return false;
    }

    public function isCreateEnabled(): bool
    {
        if (array_key_exists('create', $this->setting) && false === $this->setting['create']) {
            return false;
        }

        return true;
    }

    public function isDeleteEnabled(): bool
    {
        if (array_key_exists('delete', $this->setting) && false === $this->setting['delete']) {
            return false;
        }

        return true;
    }

    public function isUndeleteEnabled(): bool
    {
        if (array_key_exists('undelete', $this->setting) && false === $this->setting['undelete']) {
            return false;
        }

        return true;
    }

    public function isEditEnabled(): bool
    {
        if (array_key_exists('edit', $this->setting) && false === $this->setting['edit']) {
            return false;
        }

        return true;
    }
}
