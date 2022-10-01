<?php

namespace Parthenon\Payments\PaymentProvider\TransactionCloud;

use TransactionCloud\TransactionCloud;

final class ClientFactory
{
    public function __construct(
        private Config $config,
    ) {
    }

    public function buildClient(): TransactionCloud
    {
        return TransactionCloud::create(
            $this->config->getApiKey(),
            $this->config->getApiKeyPassword(),
            $this->config->isSandbox()
        );
    }
}
