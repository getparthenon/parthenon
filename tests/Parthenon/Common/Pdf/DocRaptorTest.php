<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 01-10-2025 ( 3 years after 2.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
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
            ->with($this->callback(function (Doc $doc) use ($html) {
                return $doc->getDocumentContent() === $html && 'pdf' === $doc->getDocumentType();
            }));

        $generator = new DocRaptorGenerator($docRaptor);
        $generator->generate($html);
    }
}
