<?php

declare(strict_types=1);

namespace Tests\Feature;

use Simple\Calendar\CalendarConfig;
use Simple\Calendar\CalendarEventsByDay;
use Simple\Calendar\CalendarGenerator;
use Simple\Calendar\TemplateManager;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakeDateProvider;

final class CalendarGeneratorCoreTest extends TestCase
{
    public function testItRendersUsingFakeDateProvider(): void
    {
        $generator = new CalendarGenerator(
            new TemplateManager(),
            new FakeDateProvider(),
            new CalendarConfig()
        );

        $html = $generator->render(2026, 3, new CalendarEventsByDay());

        self::assertIsString($html);
        self::assertStringContainsString('<table', $html);
        self::assertStringContainsString('March', $html);
    }
}
