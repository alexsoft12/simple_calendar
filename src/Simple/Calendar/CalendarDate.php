<?php

declare(strict_types=1);

namespace Simple\Calendar;

final readonly class CalendarDate
{
    public function __construct(
        public int $year,
        public int $month,
        public int $day = 1
    ) {
        if ($this->month < 1 || $this->month > 12) {
            throw new \InvalidArgumentException('Month must be between 1 and 12.');
        }
        if ($this->day < 1 || $this->day > 31) {
            throw new \InvalidArgumentException('Day must be between 1 and 31.');
        }
    }

    public function toString(): string
    {
        return sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
    }
}

