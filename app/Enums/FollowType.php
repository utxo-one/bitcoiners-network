<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum FollowType: string
{
    use EnumToArray;

    case FOLLOWING = 'following';
    case FOLLOWER = 'follower';
}