<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2020-2022.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: TBD ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\User\Exception\Gdpr\Export;

use Parthenon\User\Entity\User;
use Parthenon\User\Exception\Gdpr\NoFormatterFoundException;
use Parthenon\User\Gdpr\Export\FormatterInterface;
use Parthenon\User\Gdpr\Export\FormatterManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class FormatterManagerTest extends TestCase
{
    public function testCallsCorrectFormatter()
    {
        $user = new User();

        $data = ['level' => ['one' => 'two']];

        $formatterOne = $this->createMock(FormatterInterface::class);
        $formatterTwo = $this->createMock(FormatterInterface::class);

        $formatterOne->method('getName')->will($this->returnValue('xml'));
        $formatterOne->expects($this->never())->method('format')->with($this->equalTo($data));
        $formatterOne->expects($this->never())->method('getFileName')->with($this->equalTo($user));

        $formatterTwo->method('getName')->will($this->returnValue('json'));
        $formatterTwo->expects($this->once())->method('format')->with($this->equalTo($data))->will($this->returnValue(json_encode($data)));
        $formatterTwo->expects($this->once())->method('getFileName')->with($this->equalTo($user))->will($this->returnValue('user-export.json'));

        $formatterManager = new FormatterManager('json');
        $formatterManager->add($formatterOne);
        $formatterManager->add($formatterTwo);
        $result = $formatterManager->format($user, $data);
        $this->assertInstanceOf(Response::class, $result);
    }

    public function testThrowsNoFormatterException()
    {
        $this->expectException(NoFormatterFoundException::class);
        $user = new User();

        $data = ['level' => ['one' => 'two']];

        $formatterManager = new FormatterManager('json');
        $formatterManager->format($user, $data);
    }
}
