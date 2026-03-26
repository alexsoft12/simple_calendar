<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Simple\Calendar\PhpArrayTranslator;

final class PhpArrayTranslatorTest extends TestCase
{
    public function testItLoadsSpanishTranslations(): void
    {
        $translator = new PhpArrayTranslator('es');

        self::assertSame('Marzo', $translator->get('march'));
        self::assertSame('Domingo', $translator->get('sunday'));
    }

    public function testItFallsBackToEnglishWhenLocaleDoesNotExist(): void
    {
        $translator = new PhpArrayTranslator('xx');

        self::assertSame('March', $translator->get('march'));
    }
}
