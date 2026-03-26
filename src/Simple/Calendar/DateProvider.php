<?php

declare(strict_types=1);

namespace Simple\Calendar;

use DateTimeImmutable;
use Simple\Calendar\Contracts\IDateProvider;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\ValueObjects\MonthNames;
use Simple\Calendar\ValueObjects\DayNames;

final class DateProvider implements IDateProvider
{
    public const MONTHS_LONG = [
        '01' => 'january',
        '02' => 'february',
        '03' => 'march',
        '04' => 'april',
        '05' => 'may',
        '06' => 'june',
        '07' => 'july',
        '08' => 'august',
        '09' => 'september',
        '10' => 'october',
        '11' => 'november',
        '12' => 'december',
    ];

    public const MONTHS_SHORT = [
        '01' => 'jan',
        '02' => 'feb',
        '03' => 'mar',
        '04' => 'apr',
        '05' => 'may',
        '06' => 'jun',
        '07' => 'jul',
        '08' => 'aug',
        '09' => 'sep',
        '10' => 'oct',
        '11' => 'nov',
        '12' => 'dec',
    ];

    public const DAYS_LONG = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
    ];

    public const DAYS_SHORT = [
        'sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
    ];

    public const DAYS_ABR = [
        'su',
        'mo',
        'tu',
        'we',
        'th',
        'fr',
        'sa',
    ];

    public function __construct()
    {
    }

    public function now(): int
    {
        return (new DateTimeImmutable())->getTimestamp();
    }

    public function getDaysInMonth(int $month, int $year): int
    {
        if ($month < 1 || $month > 12) {
            return 0;
        }

        $date = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));

        return (int) $date->format('t');
    }

    public function getMonthNames(MonthFormat $format = MonthFormat::Long): MonthNames
    {
        $names = match ($format) {
            MonthFormat::Long => self::MONTHS_LONG,
            MonthFormat::Short => self::MONTHS_SHORT,
        };
        return new MonthNames($names);
    }

    public function getDayNames(DayFormat $format = DayFormat::Abr): DayNames
    {
        $names = match ($format) {
            DayFormat::Long => self::DAYS_LONG,
            DayFormat::Abr => self::DAYS_ABR,
            default => self::DAYS_SHORT,
        };
        return new DayNames($names);
    }
}