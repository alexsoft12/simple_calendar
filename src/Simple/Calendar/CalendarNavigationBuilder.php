<?php

declare(strict_types=1);

namespace Simple\Calendar;

final class CalendarNavigationBuilder
{
    public function normalizeUrl(string $url): string
    {
        return preg_replace('/(.+?)\/*$/', '\\1/', $url) ?? $url;
    }

    public function buildPreviousUrl(string $baseUrl, CalendarDate $date, bool $useSegments): string
    {
        return $this->buildUrl($baseUrl, $date, $useSegments);
    }

    public function buildNextUrl(string $baseUrl, CalendarDate $date, bool $useSegments): string
    {
        return $this->buildUrl($baseUrl, $date, $useSegments);
    }

    private function buildUrl(string $baseUrl, CalendarDate $date, bool $useSegments): string
    {
        if ($useSegments) {
            return $this->normalizeUrl($baseUrl) . $date->year . '/' . str_pad((string) $date->month, 2, '0', STR_PAD_LEFT);
        }

        return rtrim($baseUrl, '/')
            . '?year=' . $date->year
            . '&month=' . str_pad((string) $date->month, 2, '0', STR_PAD_LEFT);
    }
}
