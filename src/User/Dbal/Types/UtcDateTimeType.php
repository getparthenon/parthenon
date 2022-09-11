<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\User\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Copied from https://www.doctrine-project.org/projects/doctrine-orm/en/2.12/cookbook/working-with-datetime.html.
 */
class UtcDateTimeType extends DateTimeType
{
    private static \DateTimeZone $userTimezone;

    /**
     * @var \DateTimeZone
     */
    private static $utc;

    public static function setTimeZone(string $timezone)
    {
        self::$userTimezone = new \DateTimeZone($timezone);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone(self::getUtc());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUtc(),
        );
        if (!$converted) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }
        $converted->setTimezone(self::getTimezone());

        return $converted;
    }

    private static function getTimezone(): \DateTimeZone
    {
        if (!isset(self::$userTimezone)) {
            return self::getUtc();
        }

        return self::$userTimezone;
    }

    private static function getUtc(): \DateTimeZone
    {
        return self::$utc ?: self::$utc = new \DateTimeZone('UTC');
    }
}
