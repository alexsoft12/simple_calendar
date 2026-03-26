<?php

declare(strict_types=1);

namespace Simple\Calendar;

final readonly class CalendarEvent
{
    public function __construct(
        public string $title = '',
        public string $content = '',
        public array $attributes = [],
    ) {
    }
}
