<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
