<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetter()
    {
        $expected = '/tmp/local/file.jpg';
        $file = new File($expected);
        $this->assertEquals($expected, $file->getPath());
    }
}
