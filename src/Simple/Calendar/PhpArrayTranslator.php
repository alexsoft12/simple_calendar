<?php

declare(strict_types=1);

namespace Simple\Calendar;

use Simple\Calendar\Contracts\TranslatorInterface;

final class PhpArrayTranslator implements TranslatorInterface
{
    private array $messages;

    public function __construct(
        string $locale = 'en',
        ?string $translationsPath = null
    ) {
        $basePath = $translationsPath ?? __DIR__ . '/../../lang';
        $file = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'calendar.php';

        if (!is_file($file)) {
            $file = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . 'calendar.php';
        }

        $messages = require $file;
        $this->messages = is_array($messages) ? $messages : [];
    }

    public function get(string $key): string
    {
        return (string) ($this->messages[$key] ?? $key);
    }
}
