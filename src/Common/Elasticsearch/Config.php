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
