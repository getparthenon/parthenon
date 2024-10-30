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
