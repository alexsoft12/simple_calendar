<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Simple\Calendar\Contracts\IDateProvider;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\ValueObjects\MonthNames;
use Simple\Calendar\ValueObjects\DayNames;

final class FakeDateProvider implements IDateProvider
{
    public function now(): int
    {
        return strtotime('2026-03-15 12:00:00');
    }

    public function getDaysInMonth(int $month, int $year): int
    {
        if ($month === 3) {
            return 31;
        }

        return 30;
    }

    public function getMonthNames(MonthFormat $format = MonthFormat::Long): MonthNames
    {
        // Devuelve los 12 meses para cumplir con la validación del ValueObject
        $months = [
            '01' => $format === MonthFormat::Long ? 'january' : 'jan',
            '02' => $format === MonthFormat::Long ? 'february' : 'feb',
            '03' => $format === MonthFormat::Long ? 'march' : 'mar',
            '04' => $format === MonthFormat::Long ? 'april' : 'apr',
            '05' => $format === MonthFormat::Long ? 'may' : 'may',
            '06' => $format === MonthFormat::Long ? 'june' : 'jun',
            '07' => $format === MonthFormat::Long ? 'july' : 'jul',
            '08' => $format === MonthFormat::Long ? 'august' : 'aug',
            '09' => $format === MonthFormat::Long ? 'september' : 'sep',
            '10' => $format === MonthFormat::Long ? 'october' : 'oct',
            '11' => $format === MonthFormat::Long ? 'november' : 'nov',
            '12' => $format === MonthFormat::Long ? 'december' : 'dec',
        ];
        return new MonthNames($months);
    }

    public function getDayNames(DayFormat $format = DayFormat::Abr): DayNames
    {
        return new DayNames([
            'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa',
        ]);
    }
}
