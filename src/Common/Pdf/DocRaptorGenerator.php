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

namespace Parthenon\Common\Pdf;

use DocRaptor\Doc;
use DocRaptor\DocApi;
use Parthenon\Common\Exception\GeneralException;

final class DocRaptorGenerator implements GeneratorInterface
{
    public function __construct(private DocApi $docApi)
    {
    }

    public function generate(string $html)
    {
        $doc = new Doc();
        $doc->setDocumentContent($html);
        $doc->setDocumentType('pdf');

        try {
            return $this->docApi->createDoc($doc);
        } catch (GeneralException $e) {
            throw new GeneralException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
