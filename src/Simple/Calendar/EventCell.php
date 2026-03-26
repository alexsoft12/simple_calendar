<?php

declare(strict_types=1);

namespace Simple\Calendar;

final readonly class EventCell
{
    public function __construct(
        public string $content,
        public string $title,
        public int $day,
        public bool $isToday
    ) {
    }
}

