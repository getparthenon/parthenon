<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\Common\Upload;

use Parthenon\Common\Exception\Upload\NoUploaderFoundException;
use Parthenon\Common\Upload\Factory\FactoryInterface;

final class UploaderManager implements UploadManagerInterface
{
    public function __construct(private array $configs, private FactoryInterface $factory)
    {
    }

    public function getUploader(string $name = 'default'): UploaderInterface
    {
        if ('default' === $name && 1 == count($this->configs)) {
            $config = current($this->configs);
        } elseif (!isset($this->configs[$name])) {
            throw new NoUploaderFoundException(sprintf('There is no uploader by the name "%s".', $name));
        } else {
            $config = $this->configs[$name];
        }

        return $this->factory->build($config);
    }
}
