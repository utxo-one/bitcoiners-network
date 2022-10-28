<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum UserType: string
{
    use EnumToArray;
    
    case BITCOINER = 'bitcoiner';
    case SHITCOINER = 'shitcoiner';
    case NOCOINER = 'nocoiner';
}