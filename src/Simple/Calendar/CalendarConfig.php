<?php

declare(strict_types=1);

namespace Simple\Calendar;

use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\Enums\WeekDay;

final readonly class CalendarConfig
{
    public function __construct(
        public WeekDay $weekStart = WeekDay::Sunday,
        public MonthFormat $monthFormat = MonthFormat::Long,
        public DayFormat $dayFormat = DayFormat::Abr,
        public string $eventHtml = CalendarGenerator::DEFAULT_EVENT_HTML,
        public bool $showNavigation = false,
        public bool $useSegments = false,
        public string $navigationUrl = '',
        public string $locale = 'en',
        public ?string $translationsPath = null,
        public array|string $template = '',
    ) {
    }
}
