<?php

declare(strict_types=1);

namespace Simple\Calendar\Contracts;

interface ITemplateManager
{
    public function getTemplateData(array|string $template = ''): array;
}
