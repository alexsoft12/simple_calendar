<?php

declare(strict_types=1);

namespace Simple\Calendar\Contracts;

use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\ValueObjects\MonthNames;
use Simple\Calendar\ValueObjects\DayNames;

interface IDateProvider
{
    public function now(): int;
    public function getDaysInMonth(int $month, int $year): int;
    public function getMonthNames(MonthFormat $format = MonthFormat::Long): MonthNames;
    public function getDayNames(DayFormat $format = DayFormat::Abr): DayNames;
}
