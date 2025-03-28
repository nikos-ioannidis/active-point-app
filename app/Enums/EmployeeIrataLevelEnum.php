<?php

namespace App\Enums;

enum EmployeeIrataLevelEnum: string
{
    case LEVEL_1 = 'Level_1';
    case LEVEL_2 = 'Level_2';
    case LEVEL_3 = 'Level_3';
    case NONE = 'None';

    /**
     * Get all available IRATA levels as an array of strings.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get a human-readable label for the enum value.
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::LEVEL_1 => 'Level 1',
            self::LEVEL_2 => 'Level 2',
            self::LEVEL_3 => 'Level 3',
            self::NONE => 'None',
        };
    }
}
