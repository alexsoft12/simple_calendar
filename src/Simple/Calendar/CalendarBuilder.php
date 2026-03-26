<?php

declare(strict_types=1);

namespace Simple\Calendar;

use Simple\Calendar\Enums\DayFormat;
use Simple\Calendar\Enums\MonthFormat;
use Simple\Calendar\Enums\WeekDay;

final readonly class CalendarBuilder
{
    public function __construct(
        private CalendarGenerator $generator,
        private ?int $year = null,
        private ?int $month = null,
        private ?CalendarConfig $config = null,
        private ?CalendarEventsByDay $events = null,
    ) {
    }

    public function month(?int $year = null, ?int $month = null): self
    {
        return new self(
            $this->generator,
            $year,
            $month,
            $this->config,
            $this->events
        );
    }

    public function weekStartsOn(WeekDay $weekDay): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $weekDay,
            monthFormat: $config->monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $config->template,
        ));
    }

    public function monthFormat(MonthFormat $monthFormat): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $config->template,
        ));
    }

    public function dayFormat(DayFormat $dayFormat): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $config->monthFormat,
            dayFormat: $dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $config->template,
        ));
    }

    public function navigation(?string $url = null, bool $segments = false): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $config->monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: true,
            useSegments: $segments,
            navigationUrl: $url ?? $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $config->template,
        ));
    }

    public function eventHtml(string $eventHtml): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $config->monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $config->template,
        ));
    }

    public function template(array|string $template): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $config->monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $config->locale,
            translationsPath: $config->translationsPath,
            template: $template,
        ));
    }

    public function locale(string $locale, ?string $translationsPath = null): self
    {
        $config = $this->currentConfig();

        return $this->withConfig(new CalendarConfig(
            weekStart: $config->weekStart,
            monthFormat: $config->monthFormat,
            dayFormat: $config->dayFormat,
            eventHtml: $config->eventHtml,
            showNavigation: $config->showNavigation,
            useSegments: $config->useSegments,
            navigationUrl: $config->navigationUrl,
            locale: $locale,
            translationsPath: $translationsPath ?? $config->translationsPath,
            template: $config->template,
        ));
    }

    public function bootstrap4(): self
    {
        return $this->template(TemplateManager::bootstrap4());
    }

    public function events(CalendarEventsByDay|array $events): self
    {
        return new self(
            $this->generator,
            $this->year,
            $this->month,
            $this->currentConfig(),
            $events instanceof CalendarEventsByDay ? $events : $this->normalizeEvents($events)
        );
    }

    public function addEvent(
        int $day,
        CalendarEvent|CalendarEventData|string|array $event,
        ?string $title = null,
        array $attributes = []
    ): self {
        $events = $this->cloneEvents();
        $events->add($day, $this->normalizeEvent($event, $title, $attributes));

        return new self(
            $this->generator,
            $this->year,
            $this->month,
            $this->currentConfig(),
            $events
        );
    }

    public function render(): string
    {
        return $this->generator
            ->withConfig($this->currentConfig())
            ->render($this->year ?? '', $this->month ?? '', $this->events ?? new CalendarEventsByDay());
    }

    private function withConfig(CalendarConfig $config): self
    {
        return new self(
            $this->generator,
            $this->year,
            $this->month,
            $config,
            $this->events
        );
    }

    private function currentConfig(): CalendarConfig
    {
        return $this->config ?? new CalendarConfig();
    }

    private function cloneEvents(): CalendarEventsByDay
    {
        $events = new CalendarEventsByDay();

        foreach (($this->events ?? new CalendarEventsByDay())->all() as $day => $dayEvents) {
            foreach ($dayEvents as $dayEvent) {
                $events->add($day, $dayEvent);
            }
        }

        return $events;
    }

    private function normalizeEvents(array $events): CalendarEventsByDay
    {
        $calendarEvents = new CalendarEventsByDay();

        foreach ($events as $day => $event) {
            if (!is_numeric($day)) {
                continue;
            }

            if ($this->isEventList($event)) {
                foreach ($event as $eventItem) {
                    $calendarEvents->add((int) $day, $this->normalizeEvent($eventItem));
                }

                continue;
            }

            $calendarEvents->add((int) $day, $this->normalizeEvent($event));
        }

        return $calendarEvents;
    }

    private function normalizeEvent(
        CalendarEvent|CalendarEventData|string|array $event,
        ?string $title = null,
        array $attributes = []
    ): CalendarEvent|CalendarEventData {
        if ($event instanceof CalendarEvent || $event instanceof CalendarEventData) {
            return $event;
        }

        if (is_string($event)) {
            return new CalendarEvent(
                title: $title ?? '',
                content: $event,
                attributes: $attributes
            );
        }

        if (isset($event['content']) || isset($event['title']) || isset($event['attr']) || isset($event['attributes'])) {
            return new CalendarEvent(
                title: (string) ($event['title'] ?? $title ?? ''),
                content: (string) ($event['content'] ?? ''),
                attributes: (array) ($event['attributes'] ?? $event['attr'] ?? $attributes)
            );
        }

        return new CalendarEventData(
            label: (string) ($event['label'] ?? ''),
            attributes: (array) ($event['attributes'] ?? $attributes)
        );
    }

    private function isEventList(mixed $event): bool
    {
        if (!is_array($event) || $event === []) {
            return false;
        }

        $eventKeys = ['content', 'title', 'attr', 'attributes', 'label'];

        foreach ($eventKeys as $eventKey) {
            if (array_key_exists($eventKey, $event)) {
                return false;
            }
        }

        return array_is_list($event);
    }
}
