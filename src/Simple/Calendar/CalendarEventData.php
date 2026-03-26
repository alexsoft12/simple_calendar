<?php

declare(strict_types=1);

namespace Simple\Calendar;

final readonly class CalendarEventData
{
    public function __construct(
        public string $label = '',
        public array $attributes = []
    ) {
    }
}

