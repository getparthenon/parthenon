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
        private FilterManager $filterManager
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
