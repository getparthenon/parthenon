<?php

declare(strict_types=1);

/*
 * Copyright (C) 2020-2024 Iain Cambridge
 *
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <https://www.gnu.org/licenses/>.
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
