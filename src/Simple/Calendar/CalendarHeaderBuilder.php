<?php

declare(strict_types=1);

namespace Simple\Calendar;

final class CalendarHeaderBuilder
{
    private CalendarNavigationBuilder $navigationBuilder;

    public function __construct(?CalendarNavigationBuilder $navigationBuilder = null)
    {
        $this->navigationBuilder = $navigationBuilder ?? new CalendarNavigationBuilder();
    }

    public function build(
        array $templateData,
        string $navigationUrl,
        bool $useSegments,
        CalendarDate $date,
        bool $showNavigation,
        string $monthName,
        ?CalendarDate $prev = null,
        ?CalendarDate $next = null
    ): string {
        $header = ($templateData[CalendarGenerator::TEMPLATE_KEY_TABLE_OPEN] ?? '')
            . ($templateData[CalendarGenerator::TEMPLATE_KEY_HEADING_ROW_START] ?? '');

        $baseUrl = $this->navigationBuilder->normalizeUrl($navigationUrl);

        if ($showNavigation && $prev && $next) {
            $header .= str_replace(
                '{previous_url}',
                $this->navigationBuilder->buildPreviousUrl($baseUrl, $prev, $useSegments),
                $templateData[CalendarGenerator::TEMPLATE_KEY_HEADING_PREVIOUS_CELL] ?? ''
            );
        }

        $titleCell = $templateData[CalendarGenerator::TEMPLATE_KEY_HEADING_TITLE_CELL] ?? '';
        $titleCell = str_replace('{colspan}', $showNavigation ? '5' : '7', $titleCell);
        $titleCell = str_replace('{heading}', $monthName . '&nbsp;' . $date->year, $titleCell);
        $header .= $titleCell;

        if ($showNavigation && $prev && $next) {
            $header .= str_replace(
                '{next_url}',
                $this->navigationBuilder->buildNextUrl($baseUrl, $next, $useSegments),
                $templateData[CalendarGenerator::TEMPLATE_KEY_HEADING_NEXT_CELL] ?? ''
            );
        }

        $header .= $templateData[CalendarGenerator::TEMPLATE_KEY_HEADING_ROW_END] ?? '';

        return $header;
    }
}
