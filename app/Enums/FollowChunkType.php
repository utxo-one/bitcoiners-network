<?php

namespace App\Enums;

enum FollowChunkType: string
{
    case FOLLOWING = 'following';
    case FOLLOWER = 'followers';
}