<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
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
