<?php

namespace App\Enums;

enum EmailChangeWindow: int
{
    case TWENTY_FOUR_HOURS = 1440;

    public function toMinutes(): int
    {
        return $this->value;
    }
}
