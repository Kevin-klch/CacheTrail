<?php

namespace App\Enums;

/**
 * Additional roles shown as badges after the name. A user can have any number of them;
 * they grant no rights (rights come from the main role, see UserRole).
 */
enum SecondaryRole: string
{
    case Developer = 'developer';
    case Supporter = 'supporter';

    public function label(): string
    {
        return match ($this) {
            self::Developer => __('Developer'),
            self::Supporter => __('Supporter'),
        };
    }

    /**
     * Tailwind classes of the badge after the name.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Developer => 'bg-[#3d5a80] text-white',
            self::Supporter => 'bg-[#c9971c] text-white',
        };
    }

    /**
     * Text colour of the role symbol in compact places (route cards, ratings).
     */
    public function iconClasses(): string
    {
        return match ($this) {
            self::Developer => 'text-[#3d5a80]',
            self::Supporter => 'text-[#c9971c]',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Developer => 'code',
            self::Supporter => 'heart',
        };
    }
}
