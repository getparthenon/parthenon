<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Export\DataProvider;

use Parthenon\Export\Exception\InvalidDataProviderException;
use Parthenon\Export\Exception\NoDataProviderFoundException;
use Parthenon\Export\ExportRequest;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class DataProviderFetcher implements DataProviderFetcherInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function getDataProvider(ExportRequest $request): DataProviderInterface
    {
        try {
            $provider = $this->container->get($request->getDataProviderService());
        } catch (ServiceNotFoundException $exception) {
            throw new NoDataProviderFoundException(sprintf('No data provider service found for \'%s\'.', $request->getDataProviderService()), previous: $exception);
        }

        if (!$provider instanceof DataProviderInterface) {
            throw new InvalidDataProviderException(sprintf("The data provider '%s' does not implement ".DataProviderInterface::class, $request->getDataProviderService()));
        }

        return $provider;
    }
}
