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

namespace Parthenon\Billing\BillaBear;

use BillaBear\Api\CustomersApi;
use BillaBear\Api\InvoicesApi;
use BillaBear\Api\PaymentDetailsApi;
use BillaBear\Api\SubscriptionsApi;
use BillaBear\Configuration;

class SdkFactory
{
    public function __construct(
        private string $apiUrl,
        private string $apiKey,
    ) {
    }

    public function createCustomersApi(): CustomersApi
    {
        $config = $this->createConfig();

        $apiInstance = new CustomersApi(config: $config);

        return $apiInstance;
    }

    public function createPaymentDetails(): PaymentDetailsApi
    {
        $config = $this->createConfig();

        $apiInstance = new PaymentDetailsApi(config: $config);

        return $apiInstance;
    }

    public function createSubscriptionsApi(): SubscriptionsApi
    {
        $config = $this->createConfig();

        $apiInstance = new SubscriptionsApi(config: $config);

        return $apiInstance;
    }

    public function createInvoiceApi(): InvoicesApi
    {
        $config = $this->createConfig();

        $apiInstance = new InvoicesApi(config: $config);

        return $apiInstance;
    }

    public function createConfig(): Configuration
    {
        $config = Configuration::getDefaultConfiguration();
        $config->setHost($this->apiUrl);
        $config->setApiKey('X-API-Key', $this->apiKey);

        return $config;
    }
}
