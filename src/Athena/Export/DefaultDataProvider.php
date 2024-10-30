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

namespace Parthenon\Athena\Export;

use Parthenon\Athena\Filters\FilterManager;
use Parthenon\Athena\Filters\ListFilters;
use Parthenon\Athena\SectionManagerInterface;
use Parthenon\Export\DataProvider\DataProviderInterface;
use Parthenon\Export\Exception\DataProviderFailureException;
use Parthenon\Export\Exception\InvalidDataProviderParameterException;
use Parthenon\Export\ExportRequest;

final class DefaultDataProvider implements DataProviderInterface
{
    public function __construct(
        private SectionManagerInterface $sectionManager,
        private FilterManager $filterManager,
    ) {
    }

    public function getData(ExportRequest $exportRequest): iterable
    {
        $parameters = $exportRequest->getDataProviderParameters();
        if (!isset($parameters['export_type'])) {
            throw new InvalidDataProviderParameterException("The parameter 'export_type' is missing");
        }
        if (!isset($parameters['section_url_tag'])) {
            throw new InvalidDataProviderParameterException("The parameter 'section_url_tag' is missing");
        }
        if (!isset($parameters['search'])) {
            throw new InvalidDataProviderParameterException("The parameter 'search' is missing");
        }
        if (!is_array($parameters['search'])) {
            throw new InvalidDataProviderParameterException("The parameter 'search' must be an array");
        }

        try {
            $section = $this->sectionManager->getByUrlTag($parameters['section_url_tag']);
            $repository = $section->getRepository();
            $exportType = $parameters['export_type'];

            if ('all' == $exportType) {
                $listFilters = $section->buildFilters(new ListFilters($this->filterManager));

                $filters = $listFilters->getFilters($parameters['search']);
                $results = $repository->getList($filters, 'id', 'asc', -1);
            } else {
                $results = $repository->getByIds($parameters['search']);
            }

            return $results->getResults();
        } catch (\Throwable $exception) {
            throw new DataProviderFailureException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
