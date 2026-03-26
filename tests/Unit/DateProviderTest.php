<?php

declare(strict_types=1);

namespace Tests\Unit;

use Simple\Calendar\DateProvider;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use PHPUnit\Framework\TestCase;

final class DateProviderTest extends TestCase
{
    public function testItReturnsDaysInARegularMonth(): void
    {
        $provider = new DateProvider();

        $this->assertSame(31, $provider->getDaysInMonth(1, 2026));
        $this->assertSame(30, $provider->getDaysInMonth(4, 2026));
    }

    public function testItReturnsDaysInLeapYearFebruary(): void
    {
        $provider = new DateProvider();

        $this->assertSame(29, $provider->getDaysInMonth(2, 2024));
    }

    public function testItReturnsZeroForInvalidMonth(): void
    {
        $provider = new DateProvider();

        $this->assertSame(0, $provider->getDaysInMonth(0, 2026));
        $this->assertSame(0, $provider->getDaysInMonth(13, 2026));
    }

    public function testItReturnsMonthNamesByFormat(): void
    {
        $provider = new DateProvider();

        $this->assertArrayHasKey('01', $provider->getMonthNames(MonthFormat::Long));
        $this->assertArrayHasKey('01', $provider->getMonthNames(MonthFormat::Short));
    }

    public function testItReturnsDayNamesByFormat(): void
    {
        $provider = new DateProvider();

        $this->assertSame([
            'su', 'mo', 'tu', 'we', 'th', 'fr', 'sa'
        ], $provider->getDayNames(DayFormat::Abr)->toArray());
    }
}
