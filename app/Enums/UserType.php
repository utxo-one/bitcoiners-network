<?php

namespace App\Enums;

enum UserType: string
{
    case BITCOINER = 'bitcoiner';
    case SHITCOINER = 'shitcoiner';
    case NOCOINER = 'nocoiner';
}