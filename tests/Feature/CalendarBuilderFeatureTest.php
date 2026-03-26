<?php

declare(strict_types=1);

namespace Tests\Feature;

use Simple\Calendar\CalendarGenerator;
use Simple\Calendar\DateProvider;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\TemplateManager;
use PHPUnit\Framework\TestCase;

final class CalendarBuilderFeatureTest extends TestCase
{
    public function testItBuildsCalendarWithFluentApiFromPlainArrayEvents(): void
    {
        $html = (new CalendarGenerator(
            new TemplateManager(),
            new DateProvider()
        ))
            ->month(2026, 3)
            ->bootstrap4()
            ->dayFormat(DayFormat::Long)
            ->navigation('https://example.test/reports/daily-sales', true)
            ->events([
                10 => '<strong>Ventas</strong>',
                15 => [
                    [
                        'title' => 'Total ventas',
                        'content' => '<table><tr><td>100.00</td></tr></table>',
                        'attributes' => ['class' => 'sale-entry'],
                    ],
                ],
            ])
            ->render();

        self::assertStringContainsString('https://example.test/reports/daily-sales/2026/02', $html);
        self::assertStringContainsString('Sunday', $html);
        self::assertStringContainsString('<strong>Ventas</strong>', $html);
        self::assertStringContainsString('sale-entry', $html);
    }

    public function testItPreservesGeneratorConfigWhenStartingFluentBuilder(): void
    {
        $template = [
            'table_open' => '<table class="modern-calendar">',
            'heading_row_start' => '<tr>',
            'heading_previous_cell' => '<th><a href="{previous_url}">&lt;&lt;</a></th>',
            'heading_title_cell' => '<th colspan="{colspan}" id="month_year">{heading}</th>',
            'heading_next_cell' => '<th><a href="{next_url}">&gt;&gt;</a></th>',
            'heading_row_end' => '</tr>',
            'week_row_start' => '<tr>',
            'week_day_cell' => '<td class="cl_wday">{week_day}</td>',
            'week_row_end' => '</tr>',
            'cal_row_start' => '<tr class="days">',
            'cal_cell_start' => '<td class="day">',
            'cal_cell_start_today' => '<td class="day today">',
            'cal_cell_content' => '<div class="day_num">{day}</div><div class="content">{content}</div>',
            'cal_cell_content_today' => '<div class="day_num highlight">{day}</div><div class="content">{content}</div>',
            'cal_cell_no_content' => '<div class="day_num">{day}</div>',
            'cal_cell_no_content_today' => '<div class="day_num highlight">{day}</div>',
            'cal_cell_blank' => '&nbsp;',
            'cal_cell_end' => '</td>',
            'cal_cell_end_today' => '</td>',
            'cal_row_end' => '</tr>',
            'table_close' => '</table>',
        ];

        $html = (new CalendarGenerator(
            new TemplateManager(),
            new DateProvider()
        ))
            ->month(2026, 3)
            ->navigation('https://example.test/reports/daily-sales', true)
            ->locale('es')
            ->eventHtml('{event}')
            ->template($template)
            ->dayFormat(DayFormat::Long)
            ->events([
                1 => '<strong>Ventas</strong>',
            ])
            ->render();

        self::assertStringContainsString('https://example.test/reports/daily-sales/2026/02', $html);
        self::assertStringContainsString('Marzo&nbsp;2026', $html);
        self::assertStringContainsString('Domingo', $html);
        self::assertStringContainsString('modern-calendar', $html);
        self::assertStringNotContainsString('<p></p>', $html);
    }
}
