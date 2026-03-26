<?php

declare(strict_types=1);

namespace Simple\Calendar;

final class CalendarEventsByDay
{
    /**
     * @var array<int, array<int, CalendarEvent|CalendarEventData>>
     */
    private array $items = [];

    public function add(int $day, CalendarEvent|CalendarEventData $event): void
    {
        $this->items[$day][] = $event;
    }

    /**
     * @return array<int, array<int, CalendarEvent|CalendarEventData>>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return array<int, CalendarEvent|CalendarEventData>
     */
    public function forDay(int $day): array
    {
        return $this->items[$day] ?? [];
    }

    public function hasDay(int $day): bool
    {
        return isset($this->items[$day]);
    }
}
