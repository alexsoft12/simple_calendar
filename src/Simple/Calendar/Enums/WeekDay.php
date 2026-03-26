<?php

declare(strict_types=1);

namespace Simple\Calendar\Enums;

enum WeekDay: int
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    public function label(): string
    {
        return match($this) {
            self::Sunday => 'sunday',
            self::Monday => 'monday',
            self::Tuesday => 'tuesday',
            self::Wednesday => 'wednesday',
            self::Thursday => 'thursday',
            self::Friday => 'friday',
            self::Saturday => 'saturday',
        };
    }
}

