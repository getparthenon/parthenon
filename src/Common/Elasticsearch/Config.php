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

namespace Parthenon\Common\Elasticsearch;

use Parthenon\Common\Exception\Elasticsearch\ConfigNotSetException;

final class Config
{
    public const CONNECTION_TYPE_CLOUD = 'cloud';

    public const CONNECTION_TYPE_NORMAL = 'normal';

    private string $connectionType;

    private array $hosts;

    private string $apiId;

    private string $apiKey;

    private string $elasticCloudId;

    private string $basicUsername;

    private string $basicPassword;

    public function isCloudBasedConnection(): bool
    {
        return self::CONNECTION_TYPE_CLOUD === strtolower($this->connectionType);
    }

    public function isNormalConnection(): bool
    {
        return self::CONNECTION_TYPE_NORMAL === strtolower($this->connectionType);
    }

    public function getConnectionType(): string
    {
        if (!isset($this->connectionType)) {
            throw new ConfigNotSetException('TenantConnection Type are not set');
        }

        return $this->connectionType;
    }

    public function setConnectionType(string $connectionType): void
    {
        $this->connectionType = $connectionType;
    }

    public function hasHosts(): bool
    {
        return isset($this->hosts);
    }

    public function getHosts(): array
    {
        if (!isset($this->hosts)) {
            throw new ConfigNotSetException('Hosts are not set');
        }

        return $this->hosts;
    }

    public function setHosts(array $hosts): void
    {
        $this->hosts = $hosts;
    }

    public function hasApiSettings(): bool
    {
        return isset($this->apiId) && isset($this->apiKey);
    }

    public function getApiId(): string
    {
        if (!isset($this->apiId)) {
            throw new ConfigNotSetException('API id is not set');
        }

        return $this->apiId;
    }

    public function setApiId(string $apiId): void
    {
        $this->apiId = $apiId;
    }

    public function getApiKey(): string
    {
        if (!isset($this->apiKey)) {
            throw new ConfigNotSetException('API Key is not set');
        }

        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getElasticCloudId(): string
    {
        if (!isset($this->elasticCloudId)) {
            throw new ConfigNotSetException('Elastic cloud id is not set');
        }

        return $this->elasticCloudId;
    }

    public function setElasticCloudId(string $elasticCloudId): void
    {
        $this->elasticCloudId = $elasticCloudId;
    }

    public function hasBasicAuthSettings(): bool
    {
        return isset($this->basicUsername) && isset($this->basicPassword);
    }

    public function getBasicUsername(): string
    {
        if (!isset($this->basicUsername)) {
            throw new ConfigNotSetException('Basic username is not set');
        }

        return $this->basicUsername;
    }

    public function setBasicUsername(string $basicUsername): void
    {
        $this->basicUsername = $basicUsername;
    }

    public function getBasicPassword(): string
    {
        if (!isset($this->basicPassword)) {
            throw new ConfigNotSetException('Basic password is not set');
        }

        return $this->basicPassword;
    }

    public function setBasicPassword(string $basicPassword): void
    {
        $this->basicPassword = $basicPassword;
    }
}
