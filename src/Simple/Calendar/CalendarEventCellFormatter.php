<?php

declare(strict_types=1);

namespace Simple\Calendar;

final class CalendarEventCellFormatter
{
    public function __construct(private string $eventHtml)
    {
    }

    public function format(int $day, array $eventData, bool $isToday): EventCell
    {
        $content = '';
        $title = '';
        foreach ($eventData as $event) {
            if ($event instanceof CalendarEvent) {
                $attributes = $this->formatAttributes($event->attributes);
                $content .= str_replace(
                    ['{event}', '{attr}'],
                    [$event->content, $attributes],
                    $this->eventHtml
                );
                $title .= str_replace(
                    ['{event}', '{attr}'],
                    [$event->title, $attributes],
                    $this->eventHtml
                );
            } elseif ($event instanceof CalendarEventData) {
                $attributes = $this->formatAttributes($event->attributes);
                $content .= str_replace(
                    ['{event}', '{attr}'],
                    [$event->label, $attributes],
                    $this->eventHtml
                );
                $title .= str_replace(
                    ['{event}', '{attr}'],
                    [(string) ($event->attributes['title'] ?? ''), $attributes],
                    $this->eventHtml
                );
            }
        }
        return new EventCell($content, $title, $day, $isToday);
    }

    private function formatAttributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            $attributeName = preg_replace('/[^a-zA-Z0-9_:-]/', '', (string) $key) ?? '';
            if ($attributeName === '') {
                continue;
            }

            $parts[] = sprintf(
                "%s='%s'",
                $attributeName,
                htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );
        }

        return implode(' ', $parts);
    }
}
