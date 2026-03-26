<?php

declare(strict_types=1);

namespace Simple\Calendar;

use Simple\Calendar\Contracts\IDateProvider;
use Simple\Calendar\Contracts\ITemplateManager;
use Simple\Calendar\Contracts\TranslatorInterface;
use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\Enums\WeekDay;

final class CalendarGenerator
{
    public const TEMPLATE_KEY_TABLE_OPEN = 'table_open';
    public const TEMPLATE_KEY_HEADING_ROW_START = 'heading_row_start';
    public const TEMPLATE_KEY_HEADING_PREVIOUS_CELL = 'heading_previous_cell';
    public const TEMPLATE_KEY_HEADING_TITLE_CELL = 'heading_title_cell';
    public const TEMPLATE_KEY_HEADING_NEXT_CELL = 'heading_next_cell';
    public const TEMPLATE_KEY_HEADING_ROW_END = 'heading_row_end';
    public const TEMPLATE_KEY_WEEK_ROW_START = 'week_row_start';
    public const TEMPLATE_KEY_WEEK_DAY_CELL = 'week_day_cell';
    public const TEMPLATE_KEY_WEEK_ROW_END = 'week_row_end';
    public const TEMPLATE_KEY_CAL_ROW_START = 'cal_row_start';
    public const TEMPLATE_KEY_CAL_CELL_START = 'cal_cell_start';
    public const TEMPLATE_KEY_CAL_CELL_START_TODAY = 'cal_cell_start_today';
    public const TEMPLATE_KEY_CAL_CELL_CONTENT = 'cal_cell_content';
    public const TEMPLATE_KEY_CAL_CELL_CONTENT_TODAY = 'cal_cell_content_today';
    public const TEMPLATE_KEY_CAL_CELL_NO_CONTENT = 'cal_cell_no_content';
    public const TEMPLATE_KEY_CAL_CELL_NO_CONTENT_TODAY = 'cal_cell_no_content_today';
    public const TEMPLATE_KEY_CAL_CELL_BLANK = 'cal_cell_blank';
    public const TEMPLATE_KEY_CAL_CELL_END = 'cal_cell_end';
    public const TEMPLATE_KEY_CAL_CELL_END_TODAY = 'cal_cell_end_today';
    public const TEMPLATE_KEY_CAL_ROW_END = 'cal_row_end';
    public const TEMPLATE_KEY_TABLE_CLOSE = 'table_close';

    public const DEFAULT_EVENT_HTML = '<p {attr}>{event}</p>';

    private readonly ITemplateManager $templateManager;
    private readonly IDateProvider $dateProvider;
    private readonly CalendarConfig $config;
    private readonly TranslatorInterface $translator;

    private readonly WeekDay $weekStart;
    private readonly MonthFormat $monthFormat;
    private readonly DayFormat $dayFormat;
    private readonly string $eventHtml;

    private bool $showNavigation;
    private bool $useSegments;
    private string $navigationUrl = '';
    private array $templateData = [];
    private CalendarEventCellFormatter $eventCellFormatter;
    private CalendarHeaderBuilder $headerBuilder;

    public function __construct(
        ITemplateManager $templateManager,
        IDateProvider $dateProvider,
        ?CalendarConfig $config = null,
        ?TranslatorInterface $translator = null
    ) {
        $this->templateManager = $templateManager;
        $this->dateProvider = $dateProvider;
        $this->config = $config ?? new CalendarConfig();
        $this->translator = $translator ?? new PhpArrayTranslator(
            $this->config->locale,
            $this->config->translationsPath
        );

        $this->navigationUrl = $this->config->navigationUrl;

        $this->weekStart = $this->config->weekStart;
        $this->monthFormat = $this->config->monthFormat;
        $this->dayFormat = $this->config->dayFormat;
        $this->eventHtml = $this->config->eventHtml;
        $this->showNavigation = $this->config->showNavigation;
        $this->useSegments = $this->config->useSegments;
        $this->eventCellFormatter = new CalendarEventCellFormatter($this->eventHtml);
        $this->headerBuilder = new CalendarHeaderBuilder();
    }

    public function withConfig(CalendarConfig $config): self
    {
        return new self(
            $this->templateManager,
            $this->dateProvider,
            $config
        );
    }

    public function builder(): CalendarBuilder
    {
        return new CalendarBuilder($this, config: $this->config);
    }

    public function month(?int $year = null, ?int $month = null): CalendarBuilder
    {
        return $this->builder()->month($year, $month);
    }

    public function render(
        int|string $year = '',
        int|string $month = '',
        ?CalendarEventsByDay $events = null
    ): string {
        [$year, $month] = $this->normalizeYearMonth($year, $month);

        $this->parseTemplate();

        $current = $this->getCurrentDateParts();
        $isCurrentMonth = $current['year'] === $year && $current['month'] === $month;
        $totalDays = $this->dateProvider->getDaysInMonth((int) $month, (int) $year);
        $weekStartIndex = $this->weekStart->value;
        $events = $events ?? new CalendarEventsByDay();

        $dateObj = new CalendarDate((int)$year, (int)$month);
        $prevDate = $this->getPrevMonth($dateObj);
        $nextDate = $this->getNextMonth($dateObj);
        $output = $this->headerBuilder->build(
            $this->templateData,
            $this->navigationUrl,
            $this->useSegments,
            $dateObj,
            $this->showNavigation,
            $this->getMonthName($month),
            $prevDate,
            $nextDate
        );
        $output .= $this->buildWeekDaysRow($weekStartIndex);
        $output .= $this->buildDaysRows(
            $year,
            $month,
            $totalDays,
            $weekStartIndex,
            $current,
            $isCurrentMonth,
            $events
        );
        $output .= $this->templateData[self::TEMPLATE_KEY_TABLE_CLOSE] ?? '';

        return $output;
    }

    private function parseTemplate(): void
    {
        $this->templateData = $this->templateManager->getTemplateData($this->config->template);
    }

    private function getCurrentDateParts(): array
    {
        $nowTimestamp = $this->dateProvider->now();
        $now = new \DateTimeImmutable('@' . $nowTimestamp);
        return [
            'year' => $now->format('Y'),
            'month' => $now->format('m'),
            'day' => $now->format('j'),
        ];
    }

    private function normalizeYearMonth(int|string $year, int|string $month): array
    {
        $nowTimestamp = $this->dateProvider->now();
        $now = new \DateTimeImmutable('@' . $nowTimestamp);

        if ($year === '' || !is_numeric($year)) {
            $year = $now->format('Y');
        }

        if ($month === '' || !is_numeric($month)) {
            $month = $now->format('m');
        }

        $adjusted = $this->adjustDate((int) $month, (int) $year);

        return [$adjusted['year'], $adjusted['month']];
    }

    private function adjustDate(int $month, int $year): array
    {
        while ($month > 12) {
            $month -= 12;
            $year++;
        }

        while ($month <= 0) {
            $month += 12;
            $year--;
        }

        return [
            'month' => str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            'year' => (string) $year,
        ];
    }

    private function buildWeekDaysRow(int $weekStartIndex): string
    {
        $row = $this->templateData[self::TEMPLATE_KEY_WEEK_ROW_START] ?? '';
        $dayNames = $this->dateProvider->getDayNames($this->dayFormat);

        for ($i = 0; $i < 7; $i++) {
            $key = $dayNames[($weekStartIndex + $i) % 7] ?? '';

            $row .= str_replace(
                '{week_day}',
                $this->translator->get((string) $key),
                $this->templateData[self::TEMPLATE_KEY_WEEK_DAY_CELL] ?? ''
            );
        }

        $row .= $this->templateData[self::TEMPLATE_KEY_WEEK_ROW_END] ?? '';

        return $row;
    }

    private function buildDaysRows(
        string $year,
        string $month,
        int $totalDays,
        int $weekStartIndex,
        array $current,
        bool $isCurrentMonth,
        CalendarEventsByDay $events
    ): string {
        // Usar el proveedor para obtener el primer día del mes
        $firstOfMonth = new \DateTimeImmutable(sprintf('%04d-%02d-01 12:00:00', (int) $year, (int) $month));
        $firstDayInfo = (int) $firstOfMonth->format('w');
        $day = $weekStartIndex + 1 - $firstDayInfo;

        while ($day > 1) {
            $day -= 7;
        }

        $rows = '';

        while ($day <= $totalDays) {
            $rows .= $this->templateData[self::TEMPLATE_KEY_CAL_ROW_START] ?? '';

            for ($i = 0; $i < 7; $i++) {
                $rows .= $this->buildDayCell(
                    $year,
                    $month,
                    $day,
                    $current,
                    $isCurrentMonth,
                    $totalDays,
                    $events
                );
                $day++;
            }

            $rows .= $this->templateData[self::TEMPLATE_KEY_CAL_ROW_END] ?? '';
        }

        return $rows;
    }

    private function buildDayCell(
        string $year,
        string $month,
        int $day,
        array $current,
        bool $isCurrentMonth,
        int $totalDays,
        CalendarEventsByDay $events
    ): string {
        $isToday = $isCurrentMonth && (string) $day === (string) $current['day'];
        $fullDate = $year . '-' . $month . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);

        $cell = $isToday
            ? ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_START_TODAY] ?? '')
            : ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_START] ?? '');

        $cell = str_replace('{fulldate}', $fullDate, $cell);

        if ($day > 0 && $day <= $totalDays) {
            if ($events->hasDay($day)) {
                $eventCell = $this->eventCellFormatter->format($day, $events->forDay($day), $isToday);
                $template = $isToday
                    ? ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_CONTENT_TODAY] ?? '')
                    : ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_CONTENT] ?? '');

                $cell .= str_replace(
                    ['{day}', '{title}', '{content}'],
                    [(string) $day, $eventCell->title, $eventCell->content],
                    $template
                );
            } else {
                $template = $isToday
                    ? ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_NO_CONTENT_TODAY] ?? '')
                    : ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_NO_CONTENT] ?? '');
                $cell .= str_replace('{day}', (string) $day, $template);
            }
        } else {
            $cell .= $this->templateData[self::TEMPLATE_KEY_CAL_CELL_BLANK] ?? '';
        }

        $cell .= $isToday
            ? ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_END_TODAY] ?? '')
            : ($this->templateData[self::TEMPLATE_KEY_CAL_CELL_END] ?? '');

        return $cell;
    }

    private function getMonthName(string $month): string
    {
        $monthNames = $this->dateProvider->getMonthNames($this->monthFormat);
        $monthKey = $monthNames[$month] ?? '';

        return $this->translator->get($monthKey);
    }

    private function getPrevMonth(CalendarDate $date): CalendarDate
    {
        $month = $date->month - 1;
        $year = $date->year;
        if ($month < 1) {
            $month = 12;
            $year--;
        }
        return new CalendarDate($year, $month);
    }

    private function getNextMonth(CalendarDate $date): CalendarDate
    {
        $month = $date->month + 1;
        $year = $date->year;
        if ($month > 12) {
            $month = 1;
            $year++;
        }
        return new CalendarDate($year, $month);
    }
}
