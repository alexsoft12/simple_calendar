<?php

declare(strict_types=1);

namespace Tests\Feature;

use Simple\Calendar\CalendarConfig;
use Simple\Calendar\CalendarEventsByDay;
use Simple\Calendar\CalendarGenerator;
use Simple\Calendar\DateProvider;
use Simple\Calendar\TemplateManager;
use PHPUnit\Framework\TestCase;

final class CalendarGeneratorUnitTest extends TestCase
{
    public function testItRendersCalendarHtml(): void
    {
        $generator = new CalendarGenerator(
            new TemplateManager(),
            new DateProvider(),
            new CalendarConfig()
        );

        $html = $generator->render(2026, 3, new CalendarEventsByDay());

        $this->assertIsString($html);
        $this->assertNotSame('', $html);
        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('class="table table-bordered table-sm"', $html);
    }
}
