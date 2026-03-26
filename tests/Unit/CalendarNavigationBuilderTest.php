<?php

declare(strict_types=1);

namespace Tests\Unit;

use Simple\Calendar\CalendarDate;
use Simple\Calendar\CalendarNavigationBuilder;
use PHPUnit\Framework\TestCase;

final class CalendarNavigationBuilderTest extends TestCase
{
    public function testItNormalizesUrlTrailingSlash(): void
    {
        $builder = new CalendarNavigationBuilder();

        self::assertSame(
            'https://example.test/calendar/',
            $builder->normalizeUrl('https://example.test/calendar')
        );
    }

    public function testItBuildsQueryUrl(): void
    {
        $builder = new CalendarNavigationBuilder();

        self::assertSame(
            'https://example.test/calendar?year=2026&month=03',
            $builder->buildNextUrl(
                'https://example.test/calendar/',
                new CalendarDate(2026, 3),
                false
            )
        );
    }

    public function testItBuildsSegmentUrl(): void
    {
        $builder = new CalendarNavigationBuilder();

        self::assertSame(
            'https://example.test/calendar/2026/03',
            $builder->buildPreviousUrl(
                'https://example.test/calendar/',
                new CalendarDate(2026, 3),
                true
            )
        );
    }
}
