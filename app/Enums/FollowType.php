<?php

namespace App\Enums;

enum FollowType: string
{
    case FOLLOWING = 'following';
    case FOLLOWER = 'follower';
}