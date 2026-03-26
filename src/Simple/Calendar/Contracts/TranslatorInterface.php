<?php

declare(strict_types=1);

namespace Simple\Calendar\Contracts;

interface TranslatorInterface
{
    public function get(string $key): string;
}
