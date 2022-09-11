<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Pdf;

use DocRaptor\Doc;
use DocRaptor\DocApi;
use PHPUnit\Framework\TestCase;

class DocRaptorTest extends TestCase
{
    public function testCallsDocraptorCreateDoc()
    {
        $html = '<html><body>Hello Universe</body></html>';
        $docRaptor = $this->createMock(DocApi::class);

        $docRaptor->expects($this->once())->method('createDoc')
            ->with($this->callback(function (Doc $doc) use ($html) { return $doc->getDocumentContent() === $html && 'pdf' === $doc->getDocumentType(); }));

        $generator = new DocRaptorGenerator($docRaptor);
        $generator->generate($html);
    }
}
