<?php

declare(strict_types=1);

namespace Simple\Calendar;

use InvalidArgumentException;
use Simple\Calendar\Contracts\ITemplateManager;

final class TemplateManager implements ITemplateManager
{
    public const DEFAULT_TEMPLATE = [
        'table_open' => '<div class="table-responsive"><table class="table table-bordered table-sm">',
        'heading_row_start' => '<tr>',
        'heading_previous_cell' => '<th><a href="{previous_url}">&lt;&lt;</a></th>',
        'heading_title_cell' => '<th colspan="{colspan}">{heading}</th>',
        'heading_next_cell' => '<th><a href="{next_url}">&gt;&gt;</a></th>',
        'heading_row_end' => '</tr>',
        'week_row_start' => '<tr>',
        'week_day_cell' => '<td>{week_day}</td>',
        'week_row_end' => '</tr>',
        'cal_row_start' => '<tr>',
        'cal_cell_start' => '<td data-fulldate="{fulldate}">',
        'cal_cell_start_today' => '<td data-fulldate="{fulldate}">',
        'cal_cell_content' => '<div>{day}</div><div>{content}</div>',
        'cal_cell_content_today' => '<div><strong>{day}</strong></div><div>{content}</div>',
        'cal_cell_no_content' => '<div>{day}</div>',
        'cal_cell_no_content_today' => '<div><strong>{day}</strong></div>',
        'cal_cell_blank' => '&nbsp;',
        'cal_cell_end' => '</td>',
        'cal_cell_end_today' => '</td>',
        'cal_row_end' => '</tr>',
        'table_close' => '</table></div>',
    ];

    private array $template;

    public function __construct(?array $template = null)
    {
        $this->template = $template ?? self::DEFAULT_TEMPLATE;
    }

    public function getTemplateData(array|string $template = ''): array
    {
        if ($template === '') {
            $data = $this->template;
        } elseif (is_array($template)) {
            $data = $template;
        } else {
            $data = $this->decodeTemplate($template);
        }
        $requiredKeys = array_keys(self::DEFAULT_TEMPLATE);
        $missing = array_diff($requiredKeys, array_keys($data));
        if ($missing) {
            throw new InvalidArgumentException('The custom template is missing required keys: ' . implode(', ', $missing));
        }
        return $data;
    }

    public static function bootstrap4(): array
    {
        return self::DEFAULT_TEMPLATE;
    }

    private function decodeTemplate(string $template): array
    {
        try {
            $decoded = json_decode($template, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException('Template must be a valid JSON object.', 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('The custom template must decode to an array.');
        }

        return $decoded;
    }
}
